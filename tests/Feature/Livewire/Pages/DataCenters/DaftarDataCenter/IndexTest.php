<?php

namespace Tests\Feature\Livewire\Pages\DataCenters\DaftarDataCenter;

use App\Livewire\Pages\DataCenters\DaftarDataCenter\Index;
use Livewire\Livewire;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function test_route_renders_list_actions_and_detail_drawer(): void
    {
        $response = $this->get(route('data-centers.index'))->assertOk()
            ->assertSee('Daftar Pusat Data')
            ->assertSee('DC Jakarta Alpha')
            ->assertSee('PT Awan Rekaan Digital')
            ->assertSee('Tambah Pusat Data')
            ->assertSee('Detail Pusat Data')
            ->assertSee('IT Load Digunakan')
            ->assertSee('WUE (m³/kWh)')
            ->assertSee('1.40')
            ->assertSee('0.00 m³/kWh')
            ->assertSee('max-w-2xl', false)
            ->assertSee('overscroll-contain', false)
            ->assertSee('h-dvh', false)
            ->assertSee('flex-1 overflow-y-auto', false);

        $document = new \DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new \DOMXPath($document);

        $this->assertCount(8, $xpath->query('//table/tbody/tr'));
        $this->assertCount(1, $xpath->query('//div[contains(@x-data, "drawer(")]'));

        foreach (range(1, 8) as $id) {
            $buttons = $xpath->query('//table/tbody/tr['.$id.']/td[10]//button');
            $this->assertSame(['Detail', 'Edit', 'Hapus'], array_map(
                fn ($button) => $button->getAttribute('title'), iterator_to_array($buttons),
            ));
            $this->assertSame((string) $id, $buttons->item(0)->getAttribute('data-id'));
        }

        $edit = $xpath->query('//div[contains(@x-data, "modalEdit(")]');
        $delete = $xpath->query('//div[contains(@x-data, "modalDelete(")]');
        $this->assertCount(1, $edit);
        $this->assertCount(1, $delete);
        $this->assertStringContainsString('loadDataCenter', $edit->item(0)->getAttribute('x-data'));
        $this->assertStringContainsString('saveDataCenter', $edit->item(0)->getAttribute('x-data'));
        $this->assertStringContainsString('deleteDataCenter', $delete->item(0)->getAttribute('x-data'));
    }

    public function test_drawer_groups_follow_the_create_form_and_read_livewire_state(): void
    {
        Livewire::test(Index::class)
            ->assertSee('Identitas Pusat Data')
            ->assertSee('Kapasitas & Utilisasi')
            ->assertSee('Energi dan Air')
            ->assertSee('Sertifikasi')
            ->assertSee('Belum ada sertifikasi')
            ->assertSee('Total PJBTL')
            ->assertSee('Reporting Date')
            ->assertSee('Informasi Utama')
            ->assertSee('Klasifikasi dan Layanan')
            ->assertSee('Alamat Pusat Data')
            ->assertSee('aria-label="Power Usage Effectiveness (PUE)"', false)
            ->assertSee('aria-label="Water Usage Effectiveness (WUE)"', false)
            ->assertSee('$wire.dataCenters[selectedId]?.form?.name', false)
            ->assertSee('$wire.dataCenters[selectedId]?.pue', false)
            ->assertSee('certificate.name', false);
    }

    public function test_wue_keeps_small_values_with_four_decimals(): void
    {
        Livewire::test(Index::class)
            ->assertSet('dataCenters.1.wue', '0.003')
            ->assertSet('dataCenters.6.wue', '0.0025');
    }

    public function test_edit_modal_binds_validation_errors_to_alpine_fields(): void
    {
        Livewire::test(Index::class)
            ->assertSee('loadDataCenter', false)
            ->assertSee('saveDataCenter', false)
            ->assertDontSee('@js(', false)
            ->assertSee('errors[&quot;form.name&quot;]', false)
            ->assertSee("errors['form.name']", false)
            ->assertSee("errors['certificates']", false);
    }

    public function test_search_matches_name_company_nib_location_and_status(): void
    {
        Livewire::test(Index::class)
            ->set('search', '  BATAM  ')
            ->assertSee('DC Batam Gamma')->assertDontSee('DC Jakarta Alpha')
            ->set('search', '0000000000002')
            ->assertSee('DC Bekasi Beta')->assertDontSee('DC Batam Gamma')
            ->set('search', 'Decommissioned')
            ->assertSee('DC Bandung Zeta')->assertDontSee('DC Bekasi Beta')
            ->set('search', 'tidak ditemukan')
            ->assertSee('Pusat data tidak ditemukan');
    }

    public function test_company_filter_limits_rows_to_the_selected_company(): void
    {
        Livewire::test(Index::class)
            ->set('company', '3')
            ->assertSee('DC Surabaya Delta')
            ->assertSee('DC Batam Gamma')
            ->assertDontSee('DC Jakarta Alpha');
    }

    public function test_status_filter_limits_rows_to_the_selected_status(): void
    {
        Livewire::test(Index::class)
            ->set('status', 'Planning')
            ->assertSee('DC Surabaya Delta')
            ->assertDontSee('DC Batam Gamma');
    }

    public function test_province_filter_limits_rows_to_the_selected_location(): void
    {
        Livewire::test(Index::class)
            ->set('province', 'Bali')
            ->assertSee('DC Denpasar Eta')
            ->assertDontSee('DC Jakarta Alpha');
    }

    public function test_combined_filters_intersect_and_reset_clears_them(): void
    {
        $component = Livewire::test(Index::class)
            ->set('search', 'DC')
            ->set('company', '1')
            ->set('status', 'Operational')
            ->assertSee('DC Jakarta Alpha')
            ->assertDontSee('DC Bandung Zeta')
            ->assertDontSee('DC Bekasi Beta');

        $component->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('company', '')
            ->assertSet('status', '')
            ->assertSet('province', '')
            ->assertSee('DC Bandung Zeta')
            ->assertSee('DC Bekasi Beta');
    }

    public function test_empty_state_uses_active_filters_and_offers_reset(): void
    {
        Livewire::test(Index::class)
            ->set('province', 'Bali')
            ->set('status', 'Planning')
            ->assertSee('Pusat data tidak ditemukan')
            ->assertSee('Reset filter')
            ->call('resetFilters')
            ->assertSee('DC Jakarta Alpha');
    }

    public function test_load_action_fills_edit_form_and_rejects_stale_id(): void
    {
        Livewire::test(Index::class)
            ->call('loadDataCenter', 1)
            ->assertReturned(['success' => true])
            ->assertSet('form.name', 'DC Jakarta Alpha')
            ->assertSet('form.design_load', '1500')
            ->assertSet('certificates.0.name', 'ISO 27001')
            ->call('loadDataCenter', 999)
            ->assertReturned(fn (array $result): bool => $result['success'] === false)
            ->assertSet('form.name', 'DC Jakarta Alpha');
    }

    public function test_saving_edit_updates_dummy_record_and_recalculates_metrics(): void
    {
        Livewire::test(Index::class)
            ->call('loadDataCenter', 1)
            ->set('form.name', 'DC Jakarta Utama')
            ->set('form.design_load', '1600')
            ->set('form.total_energy', '8100000')
            ->call('saveDataCenter', 1)
            ->assertReturned(['success' => true])
            ->assertSet('dataCenters.1.form.name', 'DC Jakarta Utama')
            ->assertSet('dataCenters.1.form.design_load', '1600')
            ->assertSet('dataCenters.1.pue', '1.5')
            ->assertSet('dataCenters.1.wue', '0.003')
            ->assertDispatched('toast', type: 'success', message: 'Data pusat data berhasil diperbarui dalam simulasi.');
    }

    public function test_invalid_edit_returns_errors_without_changing_dummy_record(): void
    {
        Livewire::test(Index::class)
            ->call('loadDataCenter', 1)
            ->set('form.name', '')
            ->set('form.total_energy', '100')
            ->call('saveDataCenter', 1)
            ->assertHasErrors([
                'form.name' => 'Nama Pusat Data wajib diisi.',
                'form.total_energy' => 'Konsumsi energi listrik total tidak boleh lebih kecil daripada konsumsi energi listrik IT.',
            ])
            ->assertSet('dataCenters.1.form.name', 'DC Jakarta Alpha')
            ->assertReturned(fn (array $result): bool => $result['success'] === false
                && isset($result['errors']['form.name'], $result['errors']['form.total_energy']))
            ->assertNotDispatched('toast');
    }

    public function test_certificate_rows_can_be_saved_skipped_and_removed(): void
    {
        Livewire::test(Index::class)
            ->call('loadDataCenter', 2)
            ->assertCount('certificates', 0)
            ->call('addCertificate')
            ->assertCount('certificates', 1)
            ->call('saveDataCenter', 2)
            ->assertReturned(['success' => true])
            ->assertCount('dataCenters.2.certificates', 0)
            ->set('certificates.0.name', '')
            ->set('certificates.0.issuer', 'Lembaga Uji')
            ->call('saveDataCenter', 2)
            ->assertHasErrors(['certificates.0.name' => 'Nama Sertifikasi wajib diisi.'])
            ->assertCount('dataCenters.2.certificates', 0)
            ->set('certificates.0.name', 'ISO 9001')
            ->set('certificates.0.issuer', 'Lembaga Uji')
            ->call('saveDataCenter', 2)
            ->assertReturned(['success' => true])
            ->assertSet('dataCenters.2.certificates.0.name', 'ISO 9001')
            ->call('removeCertificate', 0)
            ->assertCount('certificates', 0);
    }

    public function test_delete_removes_dummy_record_and_closes_drawer(): void
    {
        Livewire::test(Index::class)
            ->call('deleteDataCenter', 8)
            ->assertReturned(['success' => true])
            ->assertCount('dataCenters', 7)
            ->assertDontSee('DC Jayapura Nu')
            ->assertDispatched('toast', type: 'success', message: 'Pusat data berhasil dihapus dalam simulasi.')
            ->assertDispatched('drawer:close', name: 'data-center-detail')
            ->call('deleteDataCenter', 8)
            ->assertReturned(fn (array $result): bool => $result['success'] === false)
            ->assertCount('dataCenters', 7);
    }
}
