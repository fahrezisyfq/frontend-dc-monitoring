<?php

namespace App\Livewire\Pages\DataCenters\DaftarDataCenter;

use App\Livewire\Pages\Areas\Index as Areas;
use App\Livewire\Pages\DataCenters\TambahDataCenter\Create;
use App\Livewire\Pages\Services\Index as Services;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Index extends Component
{
    public const STATUS_VARIANTS = [
        'Operational' => 'primary',
        'Construction' => 'info',
        'Planning' => 'warning',
        'Decommissioned' => 'neutral',
    ];

    private const COMPANIES = [
        1 => ['name' => 'PT Awan Rekaan Digital', 'nib' => '0000000000001'],
        2 => ['name' => 'PT Cakrawala Data Fiktif', 'nib' => '0000000000002'],
        3 => ['name' => 'PT Sagara Komputasi Demo', 'nib' => '0000000000003'],
        4 => ['name' => 'PT Lentera Infrastruktur Rekaan', 'nib' => '0000000000004'],
        5 => ['name' => 'PT Arunika Server Fiktif', 'nib' => '0000000000005'],
        6 => ['name' => 'PT Nirmala Jaringan Demo', 'nib' => '0000000000006'],
    ];

    private const EXTRA_REGIONS = [
        'Jawa Barat' => ['timezone' => 'WIB', 'cities' => [
            'Bandung' => ['Coblong' => 'DEMO-04-01-01'],
            'Bekasi' => ['Cikarang' => 'DEMO-04-02-01'],
        ]],
        'Jawa Timur' => ['timezone' => 'WIB', 'cities' => [
            'Surabaya' => ['Tambaksari' => 'DEMO-05-01-01'],
        ]],
        'Kepulauan Riau' => ['timezone' => 'WIB', 'cities' => [
            'Batam' => ['Batam Kota' => 'DEMO-06-01-01'],
        ]],
        'Sumatera Utara' => ['timezone' => 'WIB', 'cities' => [
            'Medan' => ['Medan Baru' => 'DEMO-07-01-01'],
        ]],
    ];

    // Step 2 fields are split for this page's detail/edit sections.
    private const UTILIZATION_KEYS = ['design_load', 'used_load'];

    private const ENERGY_KEYS = ['it_energy', 'total_energy', 'pjbtl', 'water', 'reporting_year'];

    private const EMPTY_FORM = [
        'name' => '',
        'company' => '',
        'status' => '',
        'construction_year' => '',
        'operation_year' => '',
        'province' => '',
        'city' => '',
        'district' => '',
        'ai_ready' => '',
        'area' => '',
        'services' => [],
        'land_area' => '',
        'building_area' => '',
        'design_load' => '',
        'used_load' => '',
        'pjbtl' => '',
        'reporting_year' => '',
        'it_energy' => '',
        'total_energy' => '',
        'water' => '',
    ];

    #[Locked]
    public array $dataCenters = [];

    #[Locked]
    public array $companies = [];

    #[Locked]
    public array $regions = [];

    #[Locked]
    public array $areas = [];

    #[Locked]
    public array $serviceOptions = [];

    public string $search = '';

    public string $company = '';

    public string $status = '';

    public string $province = '';

    public array $form = [];

    public array $certificates = [];

    #[Locked]
    public ?int $selectedId = null;

    public function mount(): void
    {
        $this->companies = self::COMPANIES;
        $this->regions = array_merge(Create::REGIONS, self::EXTRA_REGIONS);

        $areaSource = app(Areas::class);
        $areaSource->mount();
        $this->areas = array_map(fn (array $area): string => $area['name'], $areaSource->areas);

        $serviceSource = app(Services::class);
        $serviceSource->mount();
        $services = array_column($serviceSource->services, null, 'name');
        foreach (Create::SERVICES as $name) {
            $sourceName = $name === 'Servers Provision' ? 'Server Provision' : $name;
            $this->serviceOptions[$name] = $services[$sourceName]['description'] ?? '';
        }

        $this->dataCenters = $this->sampleCenters();
    }

    public function loadDataCenter(int $id): array
    {
        if (! isset($this->dataCenters[$id])) {
            return ['success' => false, 'message' => 'Pusat data tidak ditemukan. Silakan buka kembali dialog.'];
        }

        $this->resetValidation();
        $this->selectedId = $id;
        $this->form = $this->dataCenters[$id]['form'];
        $this->certificates = $this->dataCenters[$id]['certificates'];

        return ['success' => true];
    }

    public function saveDataCenter(int $id): array
    {
        if (! isset($this->dataCenters[$id])) {
            $this->dispatch('toast', type: 'error', message: 'Terjadi kesalahan. Silakan coba lagi.');

            return ['success' => false, 'message' => 'Pusat data tidak ditemukan. Silakan buka kembali dialog.'];
        }

        $this->selectedId = $id;

        try {
            $this->validate($this->validationRules(), $this->validationMessages(), $this->validationAttributes());
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->errors());

            return ['success' => false, 'errors' => $exception->errors()];
        }

        $form = array_merge(self::EMPTY_FORM, $this->form);
        $form['services'] = array_values($form['services'] ?? []);

        $center = $this->dataCenters[$id];
        $center['form'] = $form;
        $center['certificates'] = $this->validCertificateRows();
        $center['company_name'] = $this->companies[(int) $form['company']]['name'] ?? '';
        $center['company_nib'] = $this->companies[(int) $form['company']]['nib'] ?? '';
        $center['pum'] = $this->regions[$form['province']]['cities'][$form['city']][$form['district']] ?? '';
        $center['timezone'] = $this->regions[$form['province']]['timezone'] ?? '';
        $center['pue'] = $this->ratio($form['it_energy'], $form['total_energy'], true);
        $center['wue'] = $this->ratio($form['it_energy'], $form['water'], false, 4);

        $this->dataCenters[$id] = $center;
        $this->resetValidation();
        $this->dispatch('toast', type: 'success', message: 'Data pusat data berhasil diperbarui dalam simulasi.');

        return ['success' => true];
    }

    public function deleteDataCenter(int $id): array
    {
        if (! isset($this->dataCenters[$id])) {
            $this->dispatch('toast', type: 'error', message: 'Terjadi kesalahan. Silakan coba lagi.');

            return ['success' => false, 'message' => 'Pusat data tidak ditemukan. Silakan buka kembali dialog.'];
        }

        unset($this->dataCenters[$id]);

        if ($this->selectedId === $id) {
            $this->selectedId = null;
        }

        $this->dispatch('toast', type: 'success', message: 'Pusat data berhasil dihapus dalam simulasi.');
        $this->dispatch('drawer:close', name: 'data-center-detail');

        return ['success' => true];
    }

    public function addCertificate(): void
    {
        $this->certificates[] = $this->emptyCertificate();
    }

    public function removeCertificate(int $index): void
    {
        unset($this->certificates[$index]);
        $this->certificates = array_values($this->certificates);
        $this->resetValidation();
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'company', 'status', 'province');
    }

    public function render(): View
    {
        $search = mb_strtolower(trim($this->search));
        $filteredDataCenters = array_filter($this->dataCenters, function (array $center) use ($search): bool {
            $form = $center['form'];

            return ($search === ''
                    || str_contains(mb_strtolower($form['name']), $search)
                    || str_contains(mb_strtolower($center['company_name']), $search)
                    || str_contains(mb_strtolower($center['company_nib']), $search)
                    || str_contains(mb_strtolower($form['city'].' '.$form['province']), $search)
                    || str_contains(mb_strtolower($form['status']), $search))
                && ($this->company === '' || (string) $form['company'] === $this->company)
                && ($this->status === '' || $form['status'] === $this->status)
                && ($this->province === '' || $form['province'] === $this->province);
        });

        $provinces = array_keys($this->regions);
        sort($provinces);

        return view('livewire.pages.data-centers.daftar-data-center.index', [
            'filteredDataCenters' => $filteredDataCenters,
            'identityFields' => Create::IDENTITY_FIELDS,
            'utilizationFields' => array_intersect_key(Create::CAPACITY_FIELDS, array_flip(self::UTILIZATION_KEYS)),
            'energyFields' => array_intersect_key(Create::CAPACITY_FIELDS, array_flip(self::ENERGY_KEYS)),
            'puePreview' => $this->ratio($this->form['it_energy'] ?? '', $this->form['total_energy'] ?? '', true),
            'wuePreview' => $this->ratio($this->form['it_energy'] ?? '', $this->form['water'] ?? '', false, 4),
            'certificateFields' => Create::CERTIFICATE_FIELDS,
            'statuses' => Create::STATUSES,
            'categories' => Create::CATEGORIES,
            'provinces' => $provinces,
            'hasFilters' => $search !== '' || $this->company !== '' || $this->status !== '' || $this->province !== '',
        ])->layout('layouts.app', ['title' => 'Daftar Pusat Data']);
    }

    private function sampleCenters(): array
    {
        $certificate = [
            'name' => 'ISO 27001',
            'issuer' => 'Lembaga Sertifikasi Simulasi',
            'issued_at' => '2025-03-12',
            'expires_at' => '2028-03-12',
            'category' => 'Internasional',
            'country' => 'Indonesia',
            'scope' => 'Sistem manajemen keamanan informasi pada layanan pusat data.',
        ];

        return [
            1 => $this->makeCenter(1, 'DC Jakarta Alpha', 1, [
                'status' => 'Operational', 'construction_year' => '2019', 'operation_year' => '2021',
                'province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'district' => 'Kebayoran Baru',
                'ai_ready' => 'Ya', 'area' => 'Kawasan Pesisir Rekaan',
                'services' => ['Rack Colocation', 'Private Suite'],
                'land_area' => '2.5', 'building_area' => '12000', 'design_load' => '1500',
                'used_load' => '980', 'pjbtl' => '2000', 'reporting_year' => '2025',
                'it_energy' => '5400000', 'total_energy' => '7560000', 'water' => '16200',
            ], [$certificate]),
            2 => $this->makeCenter(2, 'DC Bandung Zeta', 1, [
                'status' => 'Decommissioned', 'construction_year' => '2016', 'operation_year' => '2018',
                'province' => 'Jawa Barat', 'city' => 'Bandung', 'district' => 'Coblong',
                'ai_ready' => 'Tidak', 'area' => 'Lahan Mandiri Demo',
                'services' => ['Caged Colocation'],
                'land_area' => '1.2', 'building_area' => '6200', 'design_load' => '600',
                'used_load' => '0', 'pjbtl' => '900', 'reporting_year' => '2024',
                'it_energy' => '', 'total_energy' => '', 'water' => '',
            ]),
            3 => $this->makeCenter(3, 'DC Bekasi Beta', 2, [
                'status' => 'Construction', 'construction_year' => '2023',
                'province' => 'Jawa Barat', 'city' => 'Bekasi', 'district' => 'Cikarang',
                'ai_ready' => 'Ya', 'area' => 'Sentra Industri Simulasi',
                'services' => ['Dedicated Data Hall', 'Servers Provision'],
                'land_area' => '4', 'building_area' => '18000', 'design_load' => '2200',
                'used_load' => '', 'pjbtl' => '3000', 'reporting_year' => '2025',
                'it_energy' => '', 'total_energy' => '', 'water' => '',
            ]),
            4 => $this->makeCenter(4, 'DC Surabaya Delta', 3, [
                'status' => 'Planning', 'construction_year' => '2026',
                'province' => 'Jawa Timur', 'city' => 'Surabaya', 'district' => 'Tambaksari',
                'ai_ready' => 'Ya', 'area' => 'Kawasan Pesisir Rekaan',
                'services' => ['Dedicated Building'],
                'land_area' => '5.5', 'building_area' => '', 'design_load' => '3000',
                'used_load' => '', 'pjbtl' => '', 'reporting_year' => '2026',
                'it_energy' => '', 'total_energy' => '', 'water' => '',
            ]),
            5 => $this->makeCenter(5, 'DC Batam Gamma', 3, [
                'status' => 'Operational', 'construction_year' => '2018', 'operation_year' => '2020',
                'province' => 'Kepulauan Riau', 'city' => 'Batam', 'district' => 'Batam Kota',
                'ai_ready' => 'Ya', 'area' => 'Kawasan Pesisir Rekaan',
                'services' => ['Rack Colocation', 'Caged Colocation', 'Servers Provision'],
                'land_area' => '3.1', 'building_area' => '14500', 'design_load' => '1800',
                'used_load' => '1350', 'pjbtl' => '2400', 'reporting_year' => '2025',
                'it_energy' => '7200000', 'total_energy' => '9360000', 'water' => '14400',
            ], [$certificate]),
            6 => $this->makeCenter(6, 'DC Medan Epsilon', 6, [
                'status' => 'Operational', 'construction_year' => '2020', 'operation_year' => '2022',
                'province' => 'Sumatera Utara', 'city' => 'Medan', 'district' => 'Medan Baru',
                'ai_ready' => 'Tidak', 'area' => 'Lahan Mandiri Demo',
                'services' => ['Rack Colocation'],
                'land_area' => '1.8', 'building_area' => '8000', 'design_load' => '950',
                'used_load' => '620', 'pjbtl' => '1300', 'reporting_year' => '2025',
                'it_energy' => '3600000', 'total_energy' => '4680000', 'water' => '9000',
            ]),
            7 => $this->makeCenter(7, 'DC Denpasar Eta', 4, [
                'status' => 'Operational', 'construction_year' => '2021', 'operation_year' => '2023',
                'province' => 'Bali', 'city' => 'Denpasar', 'district' => 'Denpasar Selatan',
                'ai_ready' => 'Ya', 'area' => 'Sentra Industri Simulasi',
                'services' => ['Private Suite', 'Dedicated Data Hall'],
                'land_area' => '2.2', 'building_area' => '9600', 'design_load' => '1100',
                'used_load' => '740', 'pjbtl' => '1500', 'reporting_year' => '2025',
                'it_energy' => '4100000', 'total_energy' => '5330000', 'water' => '8200',
            ]),
            8 => $this->makeCenter(8, 'DC Jayapura Nu', 5, [
                'status' => 'Operational', 'construction_year' => '2019', 'operation_year' => '2022',
                'province' => 'Papua', 'city' => 'Jayapura', 'district' => 'Abepura',
                'ai_ready' => 'Tidak', 'area' => 'Lahan Mandiri Demo',
                'services' => ['Caged Colocation', 'Servers Provision'],
                'land_area' => '2.8', 'building_area' => '10500', 'design_load' => '1250',
                'used_load' => '810', 'pjbtl' => '1700', 'reporting_year' => '2025',
                'it_energy' => '4500000', 'total_energy' => '5850000', 'water' => '11250',
            ]),
        ];
    }

    private function makeCenter(int $id, string $name, int $companyId, array $values, array $certificates = []): array
    {
        $form = array_merge(self::EMPTY_FORM, ['name' => $name, 'company' => (string) $companyId], $values);
        $form['services'] = array_values($form['services'] ?? []);
        $districts = $this->regions[$form['province']]['cities'][$form['city']] ?? [];

        return [
            'id' => $id,
            'company_name' => self::COMPANIES[$companyId]['name'],
            'company_nib' => self::COMPANIES[$companyId]['nib'],
            'form' => $form,
            'certificates' => array_values($certificates),
            'pum' => $districts[$form['district']] ?? '',
            'timezone' => $this->regions[$form['province']]['timezone'] ?? '',
            'pue' => $this->ratio($form['it_energy'], $form['total_energy'], true),
            'wue' => $this->ratio($form['it_energy'], $form['water'], false, 4),
        ];
    }

    private function ratio(mixed $it, mixed $value, bool $requireAtLeastIt = false, int $precision = 2): string
    {
        if (! is_numeric($it) || ! is_numeric($value) || (float) $it <= 0 || (float) $value < 0) {
            return '';
        }

        if ($requireAtLeastIt && (float) $value < (float) $it) {
            return '';
        }

        return (string) round((float) $value / (float) $it + PHP_FLOAT_EPSILON, $precision);
    }

    private function validationRules(): array
    {
        $cities = $this->regions[$this->form['province'] ?? '']['cities'] ?? [];
        $districts = $cities[$this->form['city'] ?? ''] ?? [];

        $rules = [
            'form.name' => ['required', 'string', 'max:255'],
            'form.company' => ['required', Rule::in(array_keys($this->companies))],
            'form.status' => ['required', Rule::in(Create::STATUSES)],
            'form.province' => ['required', Rule::in(array_keys($this->regions))],
            'form.city' => ['required', Rule::in(array_keys($cities))],
            'form.district' => ['nullable', Rule::in(array_keys($districts))],
            'form.ai_ready' => ['nullable', Rule::in(['Ya', 'Tidak'])],
            'form.area' => ['nullable', Rule::in(array_values($this->areas))],
            'form.services' => ['present', 'array'],
            'form.services.*' => [Rule::in(Create::SERVICES)],
            'certificates' => ['present', 'array'],
        ];

        foreach ([Create::IDENTITY_FIELDS, Create::CAPACITY_FIELDS] as $fields) {
            foreach ($fields as $key => [$label, $type]) {
                if ($key === 'name') {
                    continue;
                }

                $rules['form.'.$key] = match ($type) {
                    'number' => ['nullable', 'numeric', 'min:0'],
                    'year' => ['nullable', 'regex:/^[1-9][0-9]{3}$/'],
                    default => ['nullable', 'string', 'max:255'],
                };
            }
        }

        if (is_numeric($this->form['it_energy'] ?? null) && (float) $this->form['it_energy'] >= 0) {
            $rules['form.total_energy'][] = 'gte:form.it_energy';
        }

        foreach ($this->certificates as $index => $row) {
            if (! $this->hasContent($row)) {
                continue;
            }

            foreach (Create::CERTIFICATE_FIELDS as $key => [$label, $type]) {
                $rules["certificates.$index.$key"] = match ($type) {
                    'date' => ['nullable', 'date_format:Y-m-d'],
                    'select' => ['nullable', Rule::in(Create::CATEGORIES)],
                    'textarea' => ['nullable', 'string', 'max:2000'],
                    default => [in_array($key, ['name', 'issuer'], true) ? 'required' : 'nullable', 'string', 'max:255'],
                };
            }

            if (($row['issued_at'] ?? '') !== '') {
                $rules["certificates.$index.expires_at"][] = 'after_or_equal:certificates.'.$index.'.issued_at';
            }
        }

        return $rules;
    }

    private function validationAttributes(): array
    {
        $attributes = [
            'form.name' => 'Nama Pusat Data', 'form.company' => 'Perusahaan', 'form.status' => 'Status Pusat Data',
            'form.province' => 'Provinsi', 'form.city' => 'Kota/Kabupaten', 'form.district' => 'Kecamatan',
            'form.ai_ready' => 'AI Ready', 'form.area' => 'Kawasan', 'form.services' => 'Service Models',
            'certificates' => 'Sertifikasi',
        ];

        foreach ([Create::IDENTITY_FIELDS, Create::CAPACITY_FIELDS] as $fields) {
            foreach ($fields as $key => [$label]) {
                $attributes['form.'.$key] = $label;
            }
        }

        foreach (Create::CERTIFICATE_FIELDS as $key => [$label]) {
            foreach (array_keys($this->certificates) as $index) {
                $attributes["certificates.$index.$key"] = $label;
            }
        }

        return $attributes;
    }

    private function validationMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute tidak boleh negatif.',
            'regex' => ':attribute harus berupa tahun 4 digit.',
            'in' => 'Pilih salah satu opsi yang tersedia.',
            'date_format' => 'Gunakan tanggal yang valid dengan format YYYY-MM-DD.',
            'after_or_equal' => 'Tanggal kedaluwarsa tidak boleh lebih awal daripada tanggal diterbitkan.',
            'form.total_energy.gte' => 'Konsumsi energi listrik total tidak boleh lebih kecil daripada konsumsi energi listrik IT.',
        ];
    }

    private function validCertificateRows(): array
    {
        $rows = [];

        foreach ($this->certificates as $row) {
            if (! $this->hasContent($row)) {
                continue;
            }

            $certificate = $this->emptyCertificate();
            foreach (Create::CERTIFICATE_FIELDS as $key => [$label, $type]) {
                $value = $row[$key] ?? '';
                $certificate[$key] = is_string($value) ? trim($value) : $value;
            }
            $rows[] = $certificate;
        }

        return $rows;
    }

    private function emptyCertificate(): array
    {
        return array_fill_keys(array_keys(Create::CERTIFICATE_FIELDS), '');
    }

    private function hasContent(array $row): bool
    {
        foreach ($row as $value) {
            if (is_scalar($value) && trim((string) $value) !== '') {
                return true;
            }
        }

        return false;
    }
}
