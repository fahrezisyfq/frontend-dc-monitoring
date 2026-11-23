<?php

namespace Tests\Feature\Livewire\Pages;

use App\Livewire\Pages\Services\Index as Services;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    public function test_route_renders_inline_form_table_and_one_of_each_modal(): void
    {
        $response = $this->get(route('service-types.index'))->assertOk()
            ->assertSee('Tambah Service')->assertSee('Rack Colocation');
        $document = new \DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new \DOMXPath($document);
        $this->assertSame(['No', 'Nama Service', 'Description', 'Aksi'], array_map(
            fn ($node) => trim($node->textContent), iterator_to_array($xpath->query('//table/thead/tr/th')),
        ));
        $this->assertCount(6, $xpath->query('//table/tbody/tr'));
        $this->assertCount(1, $xpath->query('//div[contains(@x-data, "modalEdit(")]'));
        $this->assertCount(1, $xpath->query('//div[contains(@x-data, "modalDelete(")]'));
        foreach (['create', 'edit'] as $mode) {
            foreach (['name', 'description'] as $field) {
                $id = 'service-'.$mode.'-'.$field;
                $this->assertCount(1, $xpath->query('//*[@id="'.$id.'"]'));
                $this->assertCount(1, $xpath->query('//label[@for="'.$id.'"]'));
                $this->assertCount(1, $xpath->query('//*[@id="'.$id.'-error"]'));
            }
        }
    }

    public function test_search_matches_name_or_description_and_clears(): void
    {
        Livewire::test(Services::class)->set('search', ' RACK COLOCATION ')
            ->assertSee('1 dari 6 service')->assertSee('Rack Colocation')->assertDontSee('Private Suite')
            ->set('search', 'berpagar')->assertSee('Caged Colocation')->assertSee('1 dari 6 service')
            ->set('search', 'missing')->assertSee('Service tidak ditemukan')
            ->set('search', '')->assertSee('6 dari 6 service');
    }

    public function test_create_validates_and_reset_clears_errors_without_database_writes(): void
    {
        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = $query->sql;
        });
        Livewire::test(Services::class)->set('form.name', '   ')->call('createService')
            ->assertHasErrors(['form.name' => 'required'])->assertCount('services', 6)
            ->call('resetForm')->assertHasNoErrors()->assertSet('form.name', '')
            ->set('form', ['name' => ' New Service ', 'description' => ''])->call('createService')
            ->assertCount('services', 7)->assertSet('services.7.name', 'New Service')
            ->assertSet('services.7.description', '')->assertSet('form.name', '')
            ->assertDispatched('toast', type: 'success', message: 'Service berhasil ditambahkan dalam simulasi.');
        Livewire::test(Services::class)->assertCount('services', 6);
        $this->assertSame([], $queries);
    }

    public function test_edit_validates_draft_without_changing_create_form_or_other_records(): void
    {
        Livewire::test(Services::class)->set('form.name', 'Pending creation')
            ->call('saveService', ['name' => ''], 1)
            ->assertReturned(['success' => false, 'errors' => ['form.name' => ['Nama Service wajib diisi.']]])
            ->assertSet('services.1.name', 'Rack Colocation')
            ->call('saveService', ['name' => ' Updated Service ', 'description' => 'Updated description'], 1)
            ->assertReturned(['success' => true])->assertSet('services.1.name', 'Updated Service')
            ->assertSet('services.1.description', 'Updated description')
            ->assertSet('services.2.name', 'Caged Colocation')->assertSet('form.name', 'Pending creation')
            ->call('saveService', ['name' => 'Another service'], 2)
            ->assertReturned(['success' => true])->assertSet('services.2.description', '')
            ->call('saveService', ['name' => 'Missing'], 999)->assertCount('services', 6);
    }

    public function test_delete_targets_id_and_repeat_or_missing_id_does_not_remove_other_records(): void
    {
        $component = Livewire::test(Services::class)->call('deleteService', 2)
            ->assertReturned(['success' => true])->assertCount('services', 5)
            ->assertSet('services.1.name', 'Rack Colocation')
            ->call('deleteService', 2)->assertCount('services', 5)
            ->call('deleteService', 999)->assertCount('services', 5)
            ->set('form.name', 'Another Service')->call('createService')->assertSet('services.7.name', 'Another Service');
        foreach (array_keys($component->get('services')) as $id) {
            $component->call('deleteService', $id);
        }
        $component->assertSee('Belum ada Service');
    }
}
