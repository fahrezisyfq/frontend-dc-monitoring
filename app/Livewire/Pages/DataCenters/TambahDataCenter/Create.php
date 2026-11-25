<?php

namespace App\Livewire\Pages\DataCenters\TambahDataCenter;

use App\Livewire\Pages\Areas\Index as Areas;
use App\Livewire\Pages\Services\Index as Services;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Create extends Component
{
    public const STEPS = [1 => 'Identitas Pusat Data', 2 => 'Kapasitas & Utilisasi', 3 => 'Sertifikasi', 4 => 'Konfirmasi'];

    public const IDENTITY_FIELDS = [
        'name' => ['Nama Pusat Data', 'text', '', 'Contoh: DC Jakarta Alpha'],
        'land_area' => ['Luas Lahan', 'number', 'ha', 'Contoh: 2.5'],
        'building_area' => ['Luas Bangunan', 'number', 'm²', 'Contoh: 12000'],
        'operation_year' => ['Tahun Mulai Operasi', 'year', '', 'YYYY'],
        'construction_year' => ['Tahun Mulai Konstruksi', 'year', '', 'YYYY'],
    ];

    public const CAPACITY_FIELDS = [
        'design_load' => ['Kapasitas IT Load Design', 'number', 'kW', 'Contoh: 1500.5'],
        'used_load' => ['Kapasitas IT Load Digunakan', 'number', 'kW', 'Contoh: 750'],
        'it_energy' => ['Konsumsi Energi Listrik IT', 'number', 'kWh', 'Contoh: 540000'],
        'total_energy' => ['Konsumsi Energi Listrik Total', 'number', 'kWh', 'Contoh: 810000'],
        'pjbtl' => ['Total PJBTL', 'number', 'kVA', 'Contoh: 2000'],
        'water' => ['Konsumsi Air', 'number', 'm³', 'Contoh: 270000'],
        'reporting_year' => ['Reporting Date', 'year', '', 'YYYY'],
    ];

    public const CERTIFICATE_FIELDS = [
        'name' => ['Nama Sertifikasi', 'text', '', 'Contoh: ISO 27001'],
        'issuer' => ['Lembaga Penerbit', 'text', '', 'Nama lembaga penerbit'],
        'issued_at' => ['Tanggal Diterbitkan', 'date', '', ''],
        'expires_at' => ['Tanggal Kedaluwarsa', 'date', '', ''],
        'category' => ['Kategori Sertifikat', 'select', '', ''],
        'country' => ['Negara Penerbit', 'text', '', 'Contoh: Indonesia'],
        'scope' => ['Ruang Lingkup Sertifikasi', 'textarea', '', 'Deskripsi ruang lingkup sertifikasi'],
    ];

    public const STATUSES = ['Planning', 'Construction', 'Operational', 'Decommissioned'];

    public const SERVICES = ['Rack Colocation', 'Caged Colocation', 'Private Suite', 'Dedicated Data Hall', 'Dedicated Building', 'Servers Provision'];

    public const CATEGORIES = ['Internasional', 'Nasional'];

    // Illustrative mapping only, not official PUM codes.
    public const REGIONS = [
        'DKI Jakarta' => ['timezone' => 'WIB', 'cities' => [
            'Jakarta Selatan' => ['Kebayoran Baru' => 'DEMO-01-01-01', 'Pasar Minggu' => 'DEMO-01-01-02'],
            'Jakarta Timur' => ['Cakung' => 'DEMO-01-02-01', 'Duren Sawit' => 'DEMO-01-02-02'],
        ]],
        'Bali' => ['timezone' => 'WITA', 'cities' => [
            'Denpasar' => ['Denpasar Selatan' => 'DEMO-02-01-01', 'Denpasar Barat' => 'DEMO-02-01-02'],
            'Badung' => ['Kuta' => 'DEMO-02-02-01'],
        ]],
        'Papua' => ['timezone' => 'WIT', 'cities' => [
            'Jayapura' => ['Abepura' => 'DEMO-03-01-01', 'Heram' => 'DEMO-03-01-02'],
        ]],
    ];

    #[Locked]
    public array $companies = [
        1 => ['name' => 'PT Awan Rekaan Digital', 'nib' => '0000000000001'],
        2 => ['name' => 'PT Cakrawala Data Fiktif', 'nib' => '0000000000002'],
        3 => ['name' => 'PT Sagara Komputasi Demo', 'nib' => '0000000000003'],
    ];

    #[Locked]
    public array $areas = [];

    #[Locked]
    public array $serviceOptions = [];

    public function mount(): void
    {
        // Reuse the existing page-local sample sources without copying their records.
        // Simulated CRUD is not persisted between pages.
        $areaSource = app(Areas::class);
        $areaSource->mount();
        $this->areas = array_map(fn ($area) => $area['name'], $areaSource->areas);
        $serviceSource = app(Services::class);
        $serviceSource->mount();
        $serviceRecords = array_column($serviceSource->services, null, 'name');
        foreach (self::SERVICES as $name) {
            $sourceName = $name === 'Servers Provision' ? 'Server Provision' : $name;
            $this->serviceOptions[$name] = $serviceRecords[$sourceName]['description'] ?? '';
        }

    }

    public function submit(array $payload): array
    {
        $shape = Validator::make($payload, [
            'form' => ['required', 'array'],
            'certificates' => ['present', 'array'],
            'certificates.*' => ['array'],
            'newCompanies' => ['present', 'array'],
            'newCompanies.*' => ['array'],
            'newCompanies.*.name' => ['required', 'string', 'max:255'],
            'newCompanies.*.nib' => ['required', 'regex:/^[0-9]{13}$/', 'distinct', Rule::notIn(array_column($this->companies, 'nib'))],
        ], $this->messages());
        if ($shape->fails()) {
            return $this->validationFailure($shape->errors()->toArray());
        }

        $form = $payload['form'];
        $companies = $this->companies;
        foreach ($payload['newCompanies'] as $id => $company) {
            if (! ctype_digit((string) $id) || isset($companies[$id])) {
                return $this->validationFailure(['newCompanies' => ['Referensi perusahaan baru tidak valid.']]);
            }
            $companies[$id] = $company;
        }
        $rules = [];
        $attributes = [];
        foreach ([self::IDENTITY_FIELDS, self::CAPACITY_FIELDS] as $fields) {
            foreach ($fields as $key => [$label, $type]) {
                $rules['form.'.$key] = match ($type) {
                    'number' => ['nullable', 'numeric', 'min:0'],
                    'year' => ['nullable', 'regex:/^[1-9][0-9]{3}$/'],
                    default => ['required', 'string', 'max:255'],
                };
                $attributes['form.'.$key] = $label;
            }
        }
        $province = is_string($form['province'] ?? null) ? $form['province'] : '';
        $city = is_string($form['city'] ?? null) ? $form['city'] : '';
        $cities = self::REGIONS[$province]['cities'] ?? [];
        $districts = $cities[$city] ?? [];
        foreach ([
            'company' => array_keys($companies), 'status' => self::STATUSES,
            'province' => array_keys(self::REGIONS), 'city' => array_keys($cities),
            'district' => array_keys($districts), 'ai_ready' => ['Ya', 'Tidak'], 'area' => array_keys($this->areas),
        ] as $key => $values) {
            $rules['form.'.$key] = ['nullable', Rule::in($values)];
        }
        $rules['form.services'] = ['present', 'array'];
        $rules['form.services.*'] = [Rule::in(self::SERVICES)];
        if (is_numeric($form['it_energy'] ?? null) && $form['it_energy'] >= 0) {
            $rules['form.total_energy'][] = 'gte:form.it_energy';
        }
        foreach ($payload['certificates'] as $index => $certificate) {
            if (! count(array_filter($certificate, fn ($value) => ! is_scalar($value) || trim((string) $value) !== ''))) {
                continue;
            }
            foreach (self::CERTIFICATE_FIELDS as $key => [$label, $type]) {
                $rules["certificates.$index.$key"] = match ($type) {
                    'date' => ['nullable', 'date_format:Y-m-d'],
                    'select' => ['nullable', Rule::in(self::CATEGORIES)],
                    'textarea' => ['nullable', 'string', 'max:2000'],
                    default => [in_array($key, ['name', 'issuer']) ? 'required' : 'nullable', 'string', 'max:255'],
                };
                $attributes["certificates.$index.$key"] = $label;
            }
            if (($certificate['issued_at'] ?? '') !== '') {
                $rules["certificates.$index.expires_at"][] = 'after_or_equal:certificates.'.$index.'.issued_at';
            }
        }
        $validator = Validator::make($payload, $rules, $this->messages(), $attributes);
        if ($validator->fails()) {
            return $this->validationFailure($validator->errors()->toArray());
        }

        $this->dispatch('toast', type: 'success', message: 'Data pusat data berhasil dikonfirmasi (simulasi). Belum tersimpan ke database.');

        return ['success' => true];
    }

    private function validationFailure(array $errors): array
    {
        $steps = array_map(function ($key) {
            if (str_starts_with($key, 'certificates')) {
                return 3;
            }
            $field = str_starts_with($key, 'form.') ? substr($key, 5) : '';

            return array_key_exists($field, self::CAPACITY_FIELDS) ? 2 : 1;
        }, array_keys($errors));

        return ['success' => false, 'errors' => $errors, 'step' => min($steps)];
    }

    protected function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.', 'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute tidak boleh negatif.', 'max' => ':attribute terlalu panjang (maksimal :max karakter).',
            'regex' => ':attribute harus berupa tahun 4 digit.', 'newCompanies.*.nib.regex' => 'NIB harus terdiri dari 13 digit angka.',
            'not_in' => ':attribute sudah tersedia.', 'in' => 'Pilih salah satu opsi yang tersedia.',
            'date_format' => 'Gunakan tanggal yang valid dengan format YYYY-MM-DD.',
            'after_or_equal' => 'Tanggal kedaluwarsa tidak boleh lebih awal daripada tanggal diterbitkan.',
            'form.total_energy.gte' => 'Konsumsi energi listrik total tidak boleh lebih kecil daripada konsumsi energi listrik IT.',
        ];
    }

    public function render(): View
    {
        $form = array_fill_keys([
            ...array_keys(self::IDENTITY_FIELDS), ...array_keys(self::CAPACITY_FIELDS),
            'company', 'status', 'province', 'city', 'district', 'ai_ready', 'area',
        ], '');
        $form['services'] = [];

        return view('livewire.pages.data-centers.tambah-data-center.create', [
            'steps' => self::STEPS, 'identityFields' => self::IDENTITY_FIELDS, 'capacityFields' => self::CAPACITY_FIELDS,
            'certificateFields' => self::CERTIFICATE_FIELDS,
            'statuses' => self::STATUSES, 'services' => $this->serviceOptions,
            'categories' => self::CATEGORIES,
            'initialState' => [
                'form' => $form, 'companies' => $this->companies, 'areas' => $this->areas,
                'regions' => self::REGIONS, 'steps' => self::STEPS,
                'identityFields' => self::IDENTITY_FIELDS, 'capacityFields' => self::CAPACITY_FIELDS,
                'certificateFields' => self::CERTIFICATE_FIELDS,
                'statuses' => self::STATUSES, 'services' => self::SERVICES,
                'categories' => self::CATEGORIES,
            ],
        ])->layout('layouts.app', ['title' => 'Tambah Pusat Data']);
    }
}
