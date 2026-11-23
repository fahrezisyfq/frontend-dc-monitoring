<?php

namespace Tests\Feature\Livewire\Pages;

use App\Livewire\Pages\Companies\Index as Companies;
use Livewire\Livewire;
use Tests\TestCase;

class CompaniesTest extends TestCase
{
    public function test_company_page_renders_from_existing_route(): void
    {
        $response = $this->get(route('companies.index'))->assertOk()
            ->assertSee('PT Awan Rekaan Digital');
        $this->assertMatchesRegularExpression('/Tambah\s+Perusahaan/', $response->getContent());
    }

    public function test_reusable_fields_and_actions_preserve_bindings_and_accessible_references(): void
    {
        $response = $this->get(route('companies.index'))->assertOk();
        $document = new \DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new \DOMXPath($document);

        foreach (['create-company' => 'create-', 'company' => ''] as $prefix => $errorPrefix) {
            foreach (array_keys(Companies::FIELDS) as $field) {
                $id = $prefix.'-'.$field;
                $inputs = $xpath->query('//*[@id="'.$id.'"]');
                $this->assertCount(1, $inputs);
                $input = $inputs->item(0);
                $this->assertCount(1, $xpath->query('//label[@for="'.$id.'"]'));
                $this->assertTrue($input->hasAttribute('required'));
                $this->assertSame(
                    $prefix === 'create-company' ? 'form.'.$field : 'draft.'.$field,
                    $prefix === 'create-company' ? $input->getAttribute('wire:model') : $input->getAttribute('x-model'),
                );
                foreach (explode(' ', $input->getAttribute('aria-describedby')) as $descriptionId) {
                    $this->assertCount(1, $xpath->query('//*[@id="'.$descriptionId.'"]'));
                }
                $error = $xpath->query('//*[@id="'.$errorPrefix.$field.'-error"]')->item(0);
                $this->assertNotNull($error);
                if ($prefix === 'company') {
                    $this->assertStringContainsString("errorFor('".$field."'", $error->getAttribute('x-text'));
                }
            }
        }

        $body = $xpath->query('//table/tbody')->item(0);
        $this->assertSame('search', $body->getAttribute('wire:target'));
        $this->assertSame('opacity-75', $body->getAttribute('wire:loading.delay.class'));
        foreach (range(1, 6) as $id) {
            foreach (['Edit', 'Hapus'] as $title) {
                $this->assertCount(1, $xpath->query('//table/tbody/tr/td//button[@title="'.$title.'" and @data-id="'.$id.'"]'));
            }
        }

        $edit = $xpath->query('//table/tbody/tr/td//button[@title="Edit" and @data-id="1"]')->item(0);
        $delete = $xpath->query('//table/tbody/tr/td//button[@title="Hapus" and @data-id="1"]')->item(0);
        $this->assertStringContainsString('modal-edit:open', $edit->getAttribute('@click'));
        $this->assertStringContainsString('company-edit', $edit->getAttribute('@click'));
        $this->assertStringContainsString('modal-delete:open', $delete->getAttribute('@click'));
        $this->assertStringContainsString('company-delete', $delete->getAttribute('@click'));

        $editModal = $xpath->query('//div[contains(@x-data, "modalEdit(")]');
        $deleteModal = $xpath->query('//div[contains(@x-data, "modalDelete(")]');
        $this->assertCount(1, $editModal);
        $this->assertCount(1, $deleteModal);
        $this->assertStringContainsString('saveCompany', $editModal->item(0)->getAttribute('x-data'));
        $this->assertStringContainsString('deleteCompany', $deleteModal->item(0)->getAttribute('x-data'));
        $this->assertFalse($editModal->item(0)->hasAttribute('x-on:click.self'));
        $this->assertFalse($deleteModal->item(0)->hasAttribute('x-on:click.self'));
    }

    public function test_search_matches_name_and_nib_and_shows_empty_results(): void
    {
        Livewire::test(Companies::class)
            ->set('search', '  AWAN  ')
            ->assertSee('PT Awan Rekaan Digital')->assertDontSee('PT Cakrawala Data Fiktif')
            ->set('search', '0000000000002')
            ->assertSee('PT Cakrawala Data Fiktif')->assertDontSee('PT Awan Rekaan Digital')
            ->set('search', 'tidak ditemukan')
            ->assertSee('Perusahaan tidak ditemukan');
    }

    public function test_create_adds_dummy_company_and_resets_form(): void
    {
        Livewire::test(Companies::class)
            ->set('form', $this->company())
            ->call('createCompany')
            ->assertHasNoErrors()->assertCount('companies', 7)
            ->assertSet('form.company_name', '')
            ->assertSee('PT Contoh Pengujian')
            ->assertDispatched('toast', type: 'success', message: 'Perusahaan berhasil ditambahkan.');

        Livewire::test(Companies::class)->assertCount('companies', 6)
            ->assertDontSee('PT Contoh Pengujian');
    }

    public function test_required_fields_and_invalid_nib_email_do_not_change_list(): void
    {
        $component = Livewire::test(Companies::class)->call('createCompany')
            ->assertHasErrors(array_fill_keys(array_map(fn ($field) => 'form.'.$field, array_keys(Companies::FIELDS)), 'required'))
            ->assertCount('companies', 6);

        $this->assertSame('Nama Perusahaan wajib diisi.', $component->errors()->first('form.company_name'));

        foreach (['123', '12345678901234', '123456789012a'] as $nib) {
            $component->set('form', [...$this->company(), 'nib' => $nib])->call('createCompany')
                ->assertHasErrors(['form.nib' => 'regex'])
                ->assertCount('companies', 6);

            $this->assertSame('NIB harus berisi tepat 13 digit angka.', $component->errors()->first('form.nib'));
        }

        $component->set('form', [...$this->company(), 'contact_email' => 'invalid'])->call('createCompany')
            ->assertHasErrors(['form.contact_email' => 'email'])
            ->assertCount('companies', 6);

        $this->assertSame('Format email tidak valid.', $component->errors()->first('form.contact_email'));
    }

    public function test_duplicate_nib_is_rejected_for_create_and_edit(): void
    {
        $component = Livewire::test(Companies::class)
            ->set('form', [...$this->company(), 'nib' => '0000000000001'])
            ->call('createCompany')
            ->assertHasErrors(['form.nib' => 'not_in'])
            ->assertCount('companies', 6);

        $this->assertSame('NIB sudah digunakan oleh perusahaan lain.', $component->errors()->first('form.nib'));

        $component->call('saveCompany', [...$component->get('companies')[2], 'nib' => '0000000000001'], 2)
            ->assertReturned(['success' => false, 'errors' => [
                'form.nib' => ['NIB sudah digunakan oleh perusahaan lain.'],
            ]])->assertSet('companies.2.nib', '0000000000002');
    }

    public function test_edit_accepts_same_nib_and_failed_draft_discards_changes(): void
    {
        $component = Livewire::test(Companies::class);
        $original = $component->get('companies')[1];

        $component->call('saveCompany', [...$original, 'company_name' => ''], 1)
            ->assertReturned(['success' => false, 'errors' => [
                'form.company_name' => ['Nama Perusahaan wajib diisi.'],
            ]])->assertSet('companies.1.company_name', 'PT Awan Rekaan Digital');

        $component->call('saveCompany', [...$original, 'company_name' => 'PT Perubahan Demo'], 1)
            ->assertReturned(['success' => true])
            ->assertSet('companies.1.nib', '0000000000001')
            ->assertSet('companies.1.company_name', 'PT Perubahan Demo')
            ->assertDispatched('toast', type: 'success', message: 'Data perusahaan berhasil diperbarui.');

        $component->call('saveCompany', $this->company(), 1)
            ->assertReturned(['success' => true])
            ->assertSet('companies.1', $this->company());
    }

    public function test_delete_requires_confirmation_and_missing_ids_do_not_remove_records(): void
    {
        $component = Livewire::test(Companies::class)->call('deleteCompany', 1)
            ->assertReturned(['success' => true])
            ->assertCount('companies', 5)
            ->assertDontSee('PT Awan Rekaan Digital')
            ->assertDispatched('toast', type: 'success', message: 'Perusahaan berhasil dihapus.');

        $component->call('deleteCompany', 1)
            ->assertReturned(['success' => false, 'message' => 'Perusahaan tidak ditemukan. Silakan buka kembali modal.'])
            ->assertDispatched('toast', type: 'error', message: 'Terjadi kesalahan. Silakan coba lagi.')
            ->assertCount('companies', 5);

        foreach (range(2, 6) as $id) {
            $component->call('deleteCompany', $id);
        }

        $component->assertSee('Belum ada perusahaan')
            ->set('form', $this->company())->call('createCompany')
            ->assertHasNoErrors()->assertCount('companies', 1)
            ->assertSee('PT Contoh Pengujian');
    }

    public function test_local_draft_can_be_created_updated_and_deleted_without_opening_on_server(): void
    {
        Livewire::test(Companies::class)
            ->call('saveCompany', $this->company())->assertReturned(['success' => true])
            ->assertDispatched('toast', type: 'success', message: 'Perusahaan berhasil ditambahkan.')
            ->assertCount('companies', 7)->assertSet('companies.7', $this->company())
            ->call('saveCompany', [...$this->company(), 'company_name' => 'PT Edit Lokal'], 7)
            ->assertReturned(['success' => true])->assertSet('companies.7.company_name', 'PT Edit Lokal')
            ->assertDispatched('toast', type: 'success', message: 'Data perusahaan berhasil diperbarui.')
            ->call('deleteCompany', 7)->assertReturned(['success' => true])
            ->assertDispatched('toast', type: 'success', message: 'Perusahaan berhasil dihapus.')
            ->assertCount('companies', 6);
    }

    public function test_local_draft_receives_validation_errors_and_can_retry(): void
    {
        Livewire::test(Companies::class)
            ->call('saveCompany', [...$this->company(), 'nib' => '0000000000001'])
            ->assertReturned(['success' => false, 'errors' => [
                'form.nib' => ['NIB sudah digunakan oleh perusahaan lain.'],
            ]])->assertCount('companies', 6)->assertNotDispatched('toast')
            ->call('saveCompany', [...$this->company(), 'contact_email' => 'invalid'])
            ->assertReturned(['success' => false, 'errors' => [
                'form.contact_email' => ['Format email tidak valid.'],
            ]])->assertCount('companies', 6)->assertNotDispatched('toast')
            ->call('saveCompany', $this->company())->assertReturned(['success' => true])
            ->assertCount('companies', 7);
    }

    public function test_stale_ids_cannot_update_or_delete_another_company(): void
    {
        Livewire::test(Companies::class)
            ->call('saveCompany', $this->company(), 999)
            ->assertDispatched('toast', type: 'error', message: 'Terjadi kesalahan. Silakan coba lagi.')
            ->assertReturned(['success' => false, 'message' => 'Perusahaan tidak ditemukan. Silakan buka kembali modal.'])
            ->assertCount('companies', 6)
            ->call('deleteCompany', 1)->assertReturned(['success' => true])
            ->call('deleteCompany', 1)
            ->assertDispatched('toast', type: 'error', message: 'Terjadi kesalahan. Silakan coba lagi.')
            ->assertReturned(['success' => false, 'message' => 'Perusahaan tidak ditemukan. Silakan buka kembali modal.'])
            ->assertCount('companies', 5)->assertSet('companies.2.company_name', 'PT Cakrawala Data Fiktif');
    }

    public function test_reset_form_clears_create_draft_without_dispatching_toast(): void
    {
        Livewire::test(Companies::class)
            ->set('form.company_name', 'Draft sementara')
            ->call('resetForm')
            ->assertSet('form.company_name', '')
            ->assertNotDispatched('toast');
    }

    public function test_detail_drawer_replaces_expansion_and_keeps_action_order(): void
    {
        $component = Livewire::test(Companies::class)
            ->assertSee('Detail Perusahaan')
            ->assertSee('Belum ada pusat data yang terdaftar.');
        $document = new \DOMDocument;
        @$document->loadHTML($component->html());
        $xpath = new \DOMXPath($document);
        $this->assertCount(1, $xpath->query('//div[contains(@x-data, "drawer(")]'));
        $this->assertCount(6, $xpath->query('//table/tbody/tr'));
        $this->assertCount(0, $xpath->query('//*[@aria-expanded]'));
        foreach (range(1, 6) as $id) {
            $buttons = $xpath->query('//table/tbody/tr['.$id.']/td[7]//button');
            $this->assertSame(['Detail', 'Edit', 'Hapus'], array_map(
                fn ($button) => $button->getAttribute('title'), iterator_to_array($buttons),
            ));
            $this->assertSame((string) $id, $buttons->item(0)->getAttribute('data-id'));
        }
    }

    public function test_search_preserves_detail_source_and_resets_results(): void
    {
        Livewire::test(Companies::class)
            ->set('search', 'Cakrawala')->assertSee('PT Cakrawala Data Fiktif')
            ->assertDontSee('PT Awan Rekaan Digital')
            ->assertSet('companyDataCenters.1.0.name', 'DC Jakarta Alpha')
            ->set('search', '')->assertSee('PT Awan Rekaan Digital');
    }

    public function test_displayed_counts_match_drawer_data_for_every_company(): void
    {
        $component = Livewire::test(Companies::class);
        $document = new \DOMDocument;
        @$document->loadHTML($component->html());
        $xpath = new \DOMXPath($document);
        foreach ([1 => 2, 2 => 1, 3 => 2, 4 => 0, 5 => 0, 6 => 1] as $id => $count) {
            $this->assertSame((string) $count, trim($xpath->evaluate('string(//table/tbody/tr['.$id.']/td[6])')));
            $this->assertCount($count, $component->get('companyDataCenters')[$id] ?? []);
        }
    }

    public function test_edit_preserves_related_centers_and_deleted_relations_are_not_reused(): void
    {
        $component = Livewire::test(Companies::class);

        $component->call('saveCompany', [...$component->get('companies')[6], 'company_name' => 'PT Edit Demo'], 6)
            ->assertSet('companyDataCenters.6.0.name', 'DC Medan Epsilon')
            ->call('deleteCompany', 6)
            ->call('saveCompany', $this->company());

        $this->assertEmpty($component->get('companyDataCenters')[6] ?? []);
    }

    private function company(): array
    {
        return [
            'nib' => '0123456789012',
            'company_name' => 'PT Contoh Pengujian',
            'contact_name' => 'Kontak Demo',
            'contact_email' => 'demo@example.com',
            'contact_phone' => '021-555-0199',
            'company_address' => 'Jl. Pengujian No. 10, Jakarta',
        ];
    }
}
