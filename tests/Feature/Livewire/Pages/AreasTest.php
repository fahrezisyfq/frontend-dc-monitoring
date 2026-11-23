<?php

namespace Tests\Feature\Livewire\Pages;

use App\Livewire\Pages\Areas\Index as Areas;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class AreasTest extends TestCase
{
    public function test_provider_selections_are_saved_edited_and_cleared_without_changing_the_catalog(): void
    {
        $component = Livewire::test(Areas::class);
        $original = $component->get('areas');
        $catalog = $component->get('providers');
        $component->assertSet('areas.1.provider_ids', ['1'])->assertSet('areas.2.provider_ids', ['2'])
            ->set('form.name', 'Kawasan Provider')->set('form.provider_ids', ['1', '2'])
            ->call('createArea')->assertHasNoErrors()->assertSet('areas.4.provider_ids', ['1', '2'])
            ->assertSet('form.provider_ids', []);
        $draft = $component->get('areas')[4];
        $draft['provider_ids'] = ['2'];
        $component->call('saveArea', $draft, 4)->assertReturned(['success' => true])
            ->assertSet('areas.4.provider_ids', ['2']);
        $draft['provider_ids'] = [];
        $component->call('saveArea', $draft, 4)->assertReturned(['success' => true])
            ->assertSet('areas.4.provider_ids', [])->assertSet('providers', $catalog);
        foreach ($original as $id => $record) {
            $this->assertSame($record, $component->get('areas')[$id]);
        }
    }

    public function test_provider_addition_validates_and_rejects_duplicate_pairs(): void
    {
        $component = Livewire::test(Areas::class);
        $areas = $component->get('areas');
        foreach ([[], ['name' => [], 'service' => 'Internet'], ['name' => str_repeat('a', 256), 'service' => 'Internet'], ['name' => 'Test', 'service' => []], ['name' => 'Test', 'service' => str_repeat('a', 256)]] as $input) {
            $result = $component->instance()->addProvider($input);
            $this->assertFalse($result['success']);
            $this->assertNotEmpty($result['errors']);
        }
        $component->call('addProvider', ['name' => '  Provider Baru  ', 'service' => '  Internet  '])
            ->assertReturned(['success' => true, 'id' => '3'])
            ->assertSet('providers.3', ['name' => 'Provider Baru', 'service' => 'Internet'])
            ->assertSet('areas', $areas)->assertSet('form.provider_ids', []);
        $component->call('addProvider', ['name' => 'provider baru', 'service' => 'INTERNET'])
            ->assertReturned(['success' => false, 'errors' => ['name' => ['Provider dengan nama dan jenis layanan tersebut sudah tersedia.']]])
            ->assertCount('providers', 3)->assertSet('nextProviderId', 4);
        $component->call('addProvider', ['name' => 'Provider Baru', 'service' => 'Fiber'])
            ->assertReturned(['success' => true, 'id' => '4'])->assertCount('providers', 4)
            ->set('form.name', 'Kawasan Baru')->set('form.provider_ids', ['1', '3', '4'])
            ->call('createArea')->assertHasNoErrors()->assertSet('areas.4.provider_ids', ['1', '3', '4']);
    }

    public function test_unknown_duplicate_and_non_array_provider_selections_are_rejected(): void
    {
        $component = Livewire::test(Areas::class)->set('form.name', 'Kawasan Uji');
        foreach (['invalid', ['999'], ['1', '1'], [['id' => '1']]] as $ids) {
            $component->set('form.provider_ids', $ids)->call('createArea')
                ->assertHasErrors()->assertCount('areas', 3);
            $draft = $component->get('areas')[1];
            $draft['provider_ids'] = $ids;
            $result = $component->instance()->saveArea($draft, 1);
            $this->assertFalse($result['success']);
            $this->assertNotEmpty($result['errors']);
            $component->assertSet('areas.1.provider_ids', ['1']);
        }
    }

    public function test_provider_sections_render_checkboxes_inline_forms_and_detail_list(): void
    {
        $component = Livewire::test(Areas::class);
        $doc = new \DOMDocument;
        @$doc->loadHTML($component->html());
        $xpath = new \DOMXPath($doc);
        $sections = $xpath->query('//section[*[self::h3 or self::h4][text()="Informasi Provider"]]');
        $this->assertCount(3, $sections);
        foreach ($sections as $section) {
            $this->assertSame('Infrastruktur Kawasan', trim($xpath->query('preceding-sibling::section[1]/*[1]', $section)->item(0)->textContent));
            $this->assertSame('Informasi Kontak', trim($xpath->query('following-sibling::section[1]/*[1]', $section)->item(0)->textContent));
            $this->assertCount(0, $xpath->query('.//select', $section));
        }
        foreach (['create' => '$wire.form.provider_ids', 'edit' => 'draft.provider_ids'] as $mode => $binding) {
            $section = $xpath->query('//section[h4[@id="area-'.$mode.'-provider-title"]]')->item(0);
            $checkbox = $xpath->query('.//input[@type="checkbox"]', $section)->item(0);
            $this->assertSame($binding, $checkbox->getAttribute('x-model'));
            $this->assertSame('String(id)', $checkbox->getAttribute(':value'));
            foreach (['name', 'service'] as $field) {
                $input = $xpath->query('.//input[@id="area-'.$mode.'-provider-'.$field.'"]', $section)->item(0);
                $this->assertTrue($input->hasAttribute('required'));
                $this->assertSame('providerDraft.'.$field, $input->getAttribute('x-model'));
            }
        }
        $component->assertSee('Belum ada provider terdaftar')
            ->assertSee('$wire.areas[selectedId]?.provider_ids', false)
            ->assertSee('$wire.providers[id]?.name', false)
            ->assertSee('$wire.providers[id]?.service', false);
    }

    public function test_unit_groups_and_flow_field_are_visible_in_both_forms(): void
    {
        $component = Livewire::test(Areas::class);
        $doc = new \DOMDocument;
        @$doc->loadHTML($component->html());
        $xpath = new \DOMXPath($doc);
        foreach (['create', 'edit'] as $mode) {
            foreach (['total_area' => 'ha', 'desalination_flow' => 'l/s'] as $field => $unit) {
                $input = $xpath->query('//input[@id="area-'.$mode.'-'.$field.'"]')->item(0);
                $group = $input->parentNode;
                $this->assertStringContainsString('focus-within:outline-2', $group->getAttribute('class'));
                $suffix = $xpath->query('./span', $group)->item(0);
                $this->assertSame($unit, trim($suffix->textContent));
                $this->assertFalse($suffix->hasAttribute('tabindex'));
                $this->assertStringContainsString('bg-neutral-100', $suffix->getAttribute('class'));
                $this->assertStringContainsString('border-l', $suffix->getAttribute('class'));
                $this->assertSame('any', $input->getAttribute('step'));
                if ($field === 'desalination_flow') {
                    $wrapper = $group->parentNode;
                    $this->assertFalse($wrapper->hasAttribute('x-show'));
                    $this->assertFalse($wrapper->hasAttribute('x-cloak'));
                    $this->assertStringContainsString('disabled:bg-neutral-100', $input->getAttribute('class'));
                }
            }
        }
    }

    public function test_cable_landing_station_options_and_validation_for_create_and_edit(): void
    {
        $component = Livewire::test(Areas::class);
        $doc = new \DOMDocument;
        @$doc->loadHTML($component->html());
        $xpath = new \DOMXPath($doc);
        foreach (['create', 'edit'] as $mode) {
            $options = $xpath->query('//select[@id="area-'.$mode.'-cable_landing_station"]/option');
            $this->assertSame(['Pilih Cable Landing Station', 'Domestic', 'Internasional'], array_map(
                fn ($option) => trim($option->textContent), iterator_to_array($options),
            ));
        }
        $component->set('form.name', 'Kawasan Uji')->set('form.cable_landing_station', 'Invalid')
            ->call('createArea')->assertHasErrors(['form.cable_landing_station'])->assertCount('areas', 3);
        foreach (['Domestic', 'Internasional'] as $value) {
            $component->set('form.name', 'Kawasan '.$value)->set('form.cable_landing_station', $value)
                ->call('createArea')->assertHasNoErrors();
            $id = $component->get('nextId') - 1;
            $component->assertSet('areas.'.$id.'.cable_landing_station', $value);
            $draft = $component->get('areas')[1];
            $draft['cable_landing_station'] = $value;
            $component->call('saveArea', $draft, 1)->assertReturned(['success' => true])
                ->assertSet('areas.1.cable_landing_station', $value);
        }
        $draft['cable_landing_station'] = 'Invalid';
        $component->call('saveArea', $draft, 1)
            ->assertReturned(['success' => false, 'errors' => ['form.cable_landing_station' => ['Pilihan Cable Landing Station tidak valid.']]])
            ->assertSet('areas.1.cable_landing_station', 'Internasional');
    }

    public function test_route_has_grouped_fields_and_single_reusable_overlays(): void
    {
        $response = $this->get(route('area-types.index'))->assertOk()->assertSee('Tambah Kawasan')
            ->assertSee('125,50 ha')->assertSee('Detail Kawasan');
        $doc = new \DOMDocument;
        @$doc->loadHTML($response->getContent());
        $xpath = new \DOMXPath($doc);
        foreach (['modalEdit(', 'modalDelete(', 'drawer('] as $component) {
            $this->assertCount(1, $xpath->query('//div[contains(@x-data, "'.$component.'")]'));
        }
        $this->assertCount(5, $xpath->query('//table/thead/tr/th'));
        foreach (['create', 'edit'] as $mode) {
            foreach (array_diff(array_keys((new Areas)->form), ['provider_ids']) as $field) {
                $id = 'area-'.$mode.'-'.$field;
                $this->assertCount(1, $xpath->query('//*[@id="'.$id.'"]'));
                $this->assertCount(1, $xpath->query('//label[@for="'.$id.'"]'));
            }
            $this->assertSame('tel', $xpath->query('//*[@id="area-'.$mode.'-phone"]')->item(0)->getAttribute('type'));
            $this->assertSame('any', $xpath->query('//*[@id="area-'.$mode.'-total_area"]')->item(0)->getAttribute('step'));
        }
    }

    public function test_create_and_reset_preserve_phone_and_use_no_database(): void
    {
        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = $query->sql;
        });
        Livewire::test(Areas::class)->set('form.name', '  ')->call('createArea')
            ->assertHasErrors(['form.name' => 'required'])->assertCount('areas', 3)
            ->call('resetForm')->assertHasNoErrors()->set('form.name', 'Kawasan Baru')
            ->set('form.phone', '+62 021-001')->set('form.total_area', '12.50')->call('createArea')
            ->assertCount('areas', 4)->assertSet('areas.4.phone', '+62 021-001')
            ->assertSee('12,50 ha')->assertSet('form.name', '')->assertDispatched('toast');
        Livewire::test(Areas::class)->assertCount('areas', 3);
        $this->assertSame([], $queries);
    }

    public function test_numeric_category_and_email_validation_reject_invalid_input(): void
    {
        $component = Livewire::test(Areas::class)->set('form.name', 'Valid Name');
        foreach (['total_area' => -1, 'substations' => 1.5, 'electricity_number' => -1, 'email' => 'invalid', 'category' => 'invalid'] as $field => $value) {
            $component->set('form.'.$field, $value)->call('createArea')
                ->assertHasErrors(['form.'.$field])->assertCount('areas', 3)
                ->call('resetForm')->set('form.name', 'Valid Name');
        }
        $component->set('form.desalination', '1')->set('form.desalination_flow', '-0.5')
            ->call('createArea')->assertHasErrors(['form.desalination_flow']);
    }

    public function test_desalination_flow_is_cleared_on_create_and_edit_when_disabled(): void
    {
        $component = Livewire::test(Areas::class)->set('form.name', 'No Desalination')
            ->set('form.desalination', '0')->set('form.desalination_flow', '25.5')->call('createArea')
            ->assertSet('areas.4.desalination_flow', null);
        $draft = $component->get('areas')[1];
        $draft['desalination'] = '0';
        $component->set('form.name', 'Unsubmitted creation')->call('saveArea', $draft, 1)
            ->assertReturned(['success' => true])->assertSet('areas.1.desalination_flow', null)
            ->assertSet('form.name', 'Unsubmitted creation');
        $draft['desalination'] = '1';
        $draft['desalination_flow'] = '12.75';
        $component->call('saveArea', $draft, 1)->assertReturned(['success' => true])
            ->assertSet('areas.1.desalination_flow', '12.75');
        $draft['name'] = '';
        $component->call('saveArea', $draft, 1)
            ->assertReturned(['success' => false, 'errors' => ['form.name' => ['Nama Kawasan wajib diisi.']]])
            ->assertSet('areas.1.name', 'Kawasan Pesisir Rekaan');
    }

    public function test_search_delete_and_missing_ids_leave_other_records_intact(): void
    {
        $component = Livewire::test(Areas::class)->set('search', ' PESISIR ')->assertSee('1 dari 3 kawasan')
            ->set('search', 'Kawasan Industri')->assertSee('Sentra Industri Simulasi')->assertSee('1 dari 3 kawasan')
            ->set('search', 'missing')->assertSee('Kawasan tidak ditemukan')->set('search', '')
            ->call('saveArea', ['name' => 'Invalid target'], 999)->assertCount('areas', 3)
            ->call('deleteArea', 1)->assertReturned(['success' => true])->assertCount('areas', 2)
            ->call('deleteArea', 1)->assertCount('areas', 2)->assertSet('areas.2.name', 'Sentra Industri Simulasi');
        $component->call('deleteArea', 2)->call('deleteArea', 3)->assertSee('Belum ada kawasan');
    }
}
