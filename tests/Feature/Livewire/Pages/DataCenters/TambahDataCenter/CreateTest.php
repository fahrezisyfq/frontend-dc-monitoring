<?php

namespace Tests\Feature\Livewire\Pages\DataCenters\TambahDataCenter;

use App\Livewire\Pages\DataCenters\TambahDataCenter\Create;
use Livewire\Livewire;
use Tests\TestCase;

class CreateTest extends TestCase
{
    private function payload(): array
    {
        $page = app(Create::class);
        $page->mount();
        $form = $page->render()->getData()['initialState']['form'];
        $form['name'] = 'DC Uji';

        return ['form' => $form, 'certificates' => [], 'newCompanies' => []];
    }

    private function submitPayload(array $payload): array
    {
        $page = app(Create::class);
        $page->mount();

        return $page->submit($payload);
    }

    public function test_route_renders_alpine_owned_form(): void
    {
        $this->get(route('data-centers.create'))->assertOk()
            ->assertSee('dataCenterCreate', false)
            ->assertSee('data-step="5"', false)
            ->assertSee('Service Models')
            ->assertDontSee('form.providers', false)
            ->assertDontSee('<legend class="mb-2 text-sm font-medium">Provider', false)
            ->assertDontSee('wire:model', false)
            ->assertDontSee('wire:submit', false);
    }

    public function test_final_submit_returns_success_and_simulation_toast(): void
    {
        $this->assertArrayNotHasKey('providers', $this->payload()['form']);
        Livewire::test(Create::class)->call('submit', $this->payload())
            ->assertReturned(['success' => true])
            ->assertDispatched('toast', type: 'success', message: 'Data pusat data berhasil dikonfirmasi (simulasi). Belum tersimpan ke database.');
    }

    public function test_validation_returns_field_errors_and_first_invalid_step(): void
    {
        $payload = $this->payload();
        $payload['form']['name'] = '';
        $payload['form']['reporting_year'] = '26';
        $payload['form']['water'] = '-1';
        $result = $this->submitPayload($payload);
        $this->assertFalse($result['success']);
        $this->assertSame(1, $result['step']);
        foreach (['form.name', 'form.reporting_year', 'form.water'] as $key) {
            $this->assertArrayHasKey($key, $result['errors']);
        }
        $payload['form']['name'] = 'DC Uji';
        $this->assertSame(2, $this->submitPayload($payload)['step']);
    }

    public function test_energy_validation_accepts_decimals_and_rejects_total_below_it(): void
    {
        $payload = $this->payload();
        $payload['form']['it_energy'] = '100.5';
        $payload['form']['total_energy'] = '100';
        $result = $this->submitPayload($payload);
        $this->assertSame(3, $result['step']);
        $this->assertArrayHasKey('form.total_energy', $result['errors']);
        $payload['form']['total_energy'] = '100.5';
        $payload['form']['water'] = '0';
        $this->assertTrue($this->submitPayload($payload)['success']);
    }

    public function test_references_and_new_company_are_validated(): void
    {
        $payload = $this->payload();
        $payload['newCompanies'][4] = ['name' => 'Perusahaan Baru', 'nib' => '1234567890123'];
        $payload['form']['company'] = '4';
        $this->assertTrue($this->submitPayload($payload)['success']);
        $payload['newCompanies'][4]['nib'] = '123';
        $this->assertArrayHasKey('newCompanies.4.nib', $this->submitPayload($payload)['errors']);
        $payload = $this->payload();
        $payload['form']['company'] = '999';
        $payload['form']['services'] = ['Tidak tersedia'];
        $payload['form']['city'] = 'Tidak tersedia';
        $result = $this->submitPayload($payload);
        foreach (['form.company', 'form.services.0', 'form.city'] as $key) {
            $this->assertArrayHasKey($key, $result['errors']);
        }
    }

    public function test_optional_certificates_require_complete_valid_rows_when_filled(): void
    {
        $payload = $this->payload();
        $payload['certificates'][2] = ['name' => '', 'issuer' => ''];
        $this->assertTrue($this->submitPayload($payload)['success']);
        $payload['certificates'][2]['name'] = 'ISO';
        $result = $this->submitPayload($payload);
        $this->assertSame(4, $result['step']);
        $this->assertArrayHasKey('certificates.2.issuer', $result['errors']);
        $payload['certificates'][2] = ['name' => 'ISO', 'issuer' => 'Issuer', 'issued_at' => '2026-02-02', 'expires_at' => '2026-01-01'];
        $this->assertArrayHasKey('certificates.2.expires_at', $this->submitPayload($payload)['errors']);
        $payload['certificates'][2]['expires_at'] = '2027-01-01';
        $this->assertTrue($this->submitPayload($payload)['success']);
    }

    public function test_malformed_payload_is_rejected(): void
    {
        $this->assertFalse($this->submitPayload([])['success']);
        $payload = $this->payload();
        $payload['certificates'] = ['invalid'];
        $this->assertArrayHasKey('certificates.0', $this->submitPayload($payload)['errors']);
    }
}
