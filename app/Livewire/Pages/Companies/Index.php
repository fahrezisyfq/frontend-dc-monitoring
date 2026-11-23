<?php

namespace App\Livewire\Pages\Companies;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;

class Index extends Component
{
    public const FIELDS = [
        'nib' => 'NIB',
        'company_name' => 'Nama Perusahaan',
        'contact_name' => 'Nama Kontak',
        'contact_email' => 'Email',
        'contact_phone' => 'Telepon',
        'company_address' => 'Alamat Perusahaan',
    ];

    #[Locked]
    public array $companies = [];

    public string $search = '';

    #[Locked]
    public array $companyDataCenters = [
        // Page-local dummy relationships, keyed by company ID.
        1 => [
            ['name' => 'DC Jakarta Alpha', 'status' => 'Operational', 'city' => 'Jakarta, DKI Jakarta'],
            ['name' => 'DC Bandung Zeta', 'status' => 'Decommissioned', 'city' => 'Bandung, Jawa Barat'],
        ],
        2 => [
            ['name' => 'DC Bekasi Beta', 'status' => 'Construction', 'city' => 'Bekasi, Jawa Barat'],
        ],
        3 => [
            ['name' => 'DC Surabaya Delta', 'status' => 'Planning', 'city' => 'Surabaya, Jawa Timur'],
            ['name' => 'DC Batam Gamma', 'status' => 'Operational', 'city' => 'Batam, Kepulauan Riau'],
        ],
        6 => [
            ['name' => 'DC Medan Epsilon', 'status' => 'Operational', 'city' => 'Medan, Sumatera Utara'],
        ],
    ];

    public array $form = [];

    public string $createError = '';

    public function mount(): void
    {
        $samples = [
            ['PT Awan Rekaan Digital', 'Nara Pradipta', 'nara', '021-555-0101', 'Jl. Simulasi Raya No. 12, Jakarta Selatan'],
            ['PT Cakrawala Data Fiktif', 'Laras Anindita', 'laras', '022-555-0102', 'Jl. Contoh Utama No. 24, Bandung'],
            ['PT Sagara Komputasi Demo', 'Damar Wicaksana', 'damar', '031-555-0103', 'Jl. Demo Sentosa No. 8, Surabaya'],
            ['PT Lentera Infrastruktur Rekaan', 'Kirana Maheswari', 'kirana', '024-555-0104', 'Jl. Ilustrasi Indah No. 16, Semarang'],
            ['PT Arunika Server Fiktif', 'Banyu Pranata', 'banyu', '0274-555-0105', 'Jl. Simulasi Timur No. 32, Yogyakarta'],
            ['PT Nirmala Jaringan Demo', 'Senja Paramita', 'senja', '061-555-0106', 'Jl. Contoh Barat No. 5, Medan'],
        ];

        foreach ($samples as $index => [$name, $contact, $email, $phone, $address]) {
            $this->companies[$index + 1] = array_combine(array_keys(self::FIELDS), [
                '000000000000'.($index + 1), $name, $contact, $email.'@example.com', $phone, $address,
            ]);
        }

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->form = array_fill_keys(array_keys(self::FIELDS), '');
        $this->createError = '';
        $this->resetValidation(array_map(fn (string $field): string => 'form.'.$field, array_keys(self::FIELDS)));
    }

    public function createCompany(): void
    {
        $this->createError = '';
        $this->form = $this->trimDraft($this->form);
        [$rules, $attributes] = $this->validationRules();

        $validated = $this->validate($rules, $this->validationMessages(), $attributes);

        try {
            $id = empty($this->companies) ? 1 : max(array_keys($this->companies)) + 1;
            $this->companies[$id] = $validated['form'];
        } catch (Throwable $exception) {
            report($exception);
            $this->createError = 'Terjadi kesalahan. Silakan coba lagi.';
            $this->dispatch('toast', type: 'error', message: $this->createError);

            return;
        }

        $this->search = '';
        $this->resetForm();
        $this->dispatch('toast', type: 'success', message: 'Perusahaan berhasil ditambahkan.');
    }

    public function saveCompany(array $draft, ?int $id = null): array
    {
        $draft = $this->trimDraft(array_intersect_key($draft, self::FIELDS));

        if ($id !== null && ! isset($this->companies[$id])) {
            $this->dispatch('toast', type: 'error', message: 'Terjadi kesalahan. Silakan coba lagi.');

            return ['success' => false, 'message' => 'Perusahaan tidak ditemukan. Silakan buka kembali modal.'];
        }

        try {
            [$rules, $attributes] = $this->validationRules($id);
            $validator = Validator::make(['form' => $draft], $rules, $this->validationMessages(), $attributes);

            if ($validator->fails()) {
                return ['success' => false, 'errors' => $validator->errors()->toArray()];
            }

            if ($id === null) {
                $id = empty($this->companies) ? 1 : max(array_keys($this->companies)) + 1;
                $message = 'Perusahaan berhasil ditambahkan.';
            } else {
                $message = 'Data perusahaan berhasil diperbarui.';
            }

            $this->companies[$id] = $validator->validated()['form'];
            $this->search = '';
            $this->dispatch('toast', type: 'success', message: $message);

            return ['success' => true];
        } catch (Throwable $exception) {
            report($exception);
            $this->dispatch('toast', type: 'error', message: 'Terjadi kesalahan. Silakan coba lagi.');

            return ['success' => false, 'message' => 'Terjadi kesalahan. Silakan coba lagi.'];
        }
    }

    public function deleteCompany(int $id): array
    {
        if (! isset($this->companies[$id])) {
            $this->dispatch('toast', type: 'error', message: 'Terjadi kesalahan. Silakan coba lagi.');

            return ['success' => false, 'message' => 'Perusahaan tidak ditemukan. Silakan buka kembali modal.'];
        }

        try {
            unset($this->companies[$id], $this->companyDataCenters[$id]);
            $this->dispatch('toast', type: 'success', message: 'Perusahaan berhasil dihapus.');

            return ['success' => true];
        } catch (Throwable $exception) {
            report($exception);
            $this->dispatch('toast', type: 'error', message: 'Terjadi kesalahan. Silakan coba lagi.');

            return ['success' => false, 'message' => 'Terjadi kesalahan. Silakan coba lagi.'];
        }
    }

    public function render(): View
    {
        $search = mb_strtolower(trim($this->search));
        $filteredCompanies = array_filter($this->companies, fn ($company) => $search === ''
            || str_contains($company['nib'], $search)
            || str_contains(mb_strtolower($company['company_name']), $search));

        return view('livewire.pages.companies.index', [
            'filteredCompanies' => $filteredCompanies,
        ])
            ->layout('layouts.app', ['title' => 'Perusahaan']);
    }

    private function validationRules(?int $id = null): array
    {
        $others = $id === null ? $this->companies : collect($this->companies)->except($id)->all();
        $rules = [];
        $attributes = [];

        foreach (self::FIELDS as $field => $label) {
            $rules['form.'.$field] = ['required', 'string', 'max:'.($field === 'company_address' ? 1000 : 255)];
            $attributes['form.'.$field] = $label;
        }

        $rules['form.nib'] = ['required', 'string', 'regex:/^[0-9]{13}$/', Rule::notIn(array_column($others, 'nib'))];
        $rules['form.contact_email'][] = 'email';

        return [$rules, $attributes];
    }

    private function validationMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
            'form.nib.regex' => 'NIB harus berisi tepat 13 digit angka.',
            'form.nib.not_in' => 'NIB sudah digunakan oleh perusahaan lain.',
            'email' => 'Format email tidak valid.',
        ];
    }

    private function trimDraft(array $draft): array
    {
        return array_map(fn ($value) => is_string($value) ? trim($value) : $value, $draft);
    }
}
