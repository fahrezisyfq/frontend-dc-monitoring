<?php

namespace App\Livewire\Pages\Areas;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Index extends Component
{
    public const CATEGORIES = ['KEK', 'Kawasan Industri', 'NON Kawasan'];

    private const EMPTY_FORM = [
        'name' => '', 'category' => 'NON Kawasan', 'total_area' => '',
        'desalination' => '0', 'desalination_flow' => '', 'water_treatment' => '0',
        'substations' => '', 'electricity_number' => '', 'cable_landing_station' => '',
        'provider_ids' => [],
        'contact_name' => '', 'email' => '', 'phone' => '',
    ];

    #[Locked]
    public array $areas = [];

    #[Locked]
    public array $providers = [
        1 => ['name' => 'Provider Rekaan', 'service' => 'Internet Dedicated'],
        2 => ['name' => 'Provider Simulasi', 'service' => 'Fiber Optic'],
    ];

    #[Locked]
    public int $nextProviderId = 3;

    #[Locked]
    public int $nextId = 4;

    public string $search = '';

    public array $form = self::EMPTY_FORM;

    public function mount(): void
    {
        // Fictional examples only; not factual descriptions of Indonesian areas.
        $this->areas = [
            1 => ['name' => 'Kawasan Pesisir Rekaan', 'category' => 'KEK', 'total_area' => 125.5,
                'desalination' => '1', 'desalination_flow' => 25.75, 'water_treatment' => '1',
                'substations' => 2, 'electricity_number' => 4, 'cable_landing_station' => 'Domestic',
                'provider_ids' => ['1'],
                'contact_name' => 'Nara Contoh', 'email' => 'nara@example.com', 'phone' => '+62 21 555 0101'],
            2 => ['name' => 'Sentra Industri Simulasi', 'category' => 'Kawasan Industri', 'total_area' => 250,
                'desalination' => '0', 'desalination_flow' => null, 'water_treatment' => '1',
                'substations' => 3, 'electricity_number' => 6, 'cable_landing_station' => '',
                'provider_ids' => ['2'],
                'contact_name' => 'Laras Contoh', 'email' => 'laras@example.com', 'phone' => '022-555-0102'],
            3 => ['name' => 'Lahan Mandiri Demo', 'category' => 'NON Kawasan', 'total_area' => 18.25,
                'desalination' => '0', 'desalination_flow' => null, 'water_treatment' => '0',
                'substations' => 0, 'electricity_number' => 1, 'cable_landing_station' => '',
                'provider_ids' => [],
                'contact_name' => '', 'email' => '', 'phone' => ''],
        ];
    }

    public function resetForm(): void
    {
        $this->form = self::EMPTY_FORM;
        $this->resetValidation();
    }

    public function addProvider(array $input): array
    {
        foreach (['name', 'service'] as $field) {
            if (is_string($input[$field] ?? null)) {
                $input[$field] = trim($input[$field]);
            }
        }
        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'service' => ['required', 'string', 'max:255'],
        ], [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
        ], ['name' => 'Nama Provider', 'service' => 'Jenis Layanan']);
        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->toArray()];
        }
        $provider = $validator->validated();
        foreach ($this->providers as $existing) {
            if (mb_strtolower($existing['name']) === mb_strtolower($provider['name'])
                && mb_strtolower($existing['service']) === mb_strtolower($provider['service'])) {
                return ['success' => false, 'errors' => ['name' => ['Provider dengan nama dan jenis layanan tersebut sudah tersedia.']]];
            }
        }
        $id = $this->nextProviderId++;
        $this->providers[$id] = $provider;

        return ['success' => true, 'id' => (string) $id];
    }

    public function createArea(): void
    {
        $this->areas[$this->nextId] = $this->validatedArea($this->form);
        $this->nextId++;
        $this->resetForm();
        $this->dispatch('toast', type: 'success', message: 'Kawasan berhasil ditambahkan dalam simulasi.');
    }

    public function saveArea(array $draft, int $id): array
    {
        if (! isset($this->areas[$id])) {
            return ['success' => false, 'message' => 'Kawasan tidak ditemukan.'];
        }

        try {
            $data = $this->validatedArea($draft);
        } catch (ValidationException $exception) {
            return ['success' => false, 'errors' => $exception->errors()];
        }

        $this->areas[$id] = $data;
        $this->dispatch('toast', type: 'success', message: 'Kawasan berhasil diperbarui dalam simulasi.');

        return ['success' => true];
    }

    public function deleteArea(int $id): array
    {
        if (! isset($this->areas[$id])) {
            return ['success' => false, 'message' => 'Kawasan tidak ditemukan.'];
        }

        unset($this->areas[$id]);
        $this->dispatch('toast', type: 'success', message: 'Kawasan berhasil dihapus dalam simulasi.');

        return ['success' => true];
    }

    private function validatedArea(array $input): array
    {
        $input = array_intersect_key($input, self::EMPTY_FORM) + self::EMPTY_FORM;
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $input[$key] = trim($value);
            }
        }
        if ($input['desalination'] === '0') {
            $input['desalination_flow'] = null;
        }
        foreach (['total_area', 'desalination_flow', 'substations', 'electricity_number'] as $field) {
            if ($input[$field] === '') {
                $input[$field] = null;
            }
        }

        $data = Validator::make(['form' => $input], [
            'form.name' => ['required', 'string', 'max:255'],
            'form.category' => ['required', Rule::in(self::CATEGORIES)],
            'form.total_area' => ['nullable', 'numeric', 'min:0'],
            'form.desalination' => ['required', Rule::in(['0', '1'])],
            'form.desalination_flow' => ['nullable', 'numeric', 'min:0'],
            'form.water_treatment' => ['required', Rule::in(['0', '1'])],
            'form.substations' => ['nullable', 'integer', 'min:0'],
            'form.electricity_number' => ['nullable', 'integer', 'min:0'],
            'form.cable_landing_station' => ['nullable', Rule::in(['Domestic', 'Internasional'])],
            'form.provider_ids' => ['array'],
            'form.provider_ids.*' => ['required', 'string', 'distinct', Rule::in(array_keys($this->providers))],
            'form.contact_name' => ['nullable', 'string', 'max:255'],
            'form.email' => ['nullable', 'email', 'max:255'],
            'form.phone' => ['nullable', 'string', 'max:50'],
        ], [
            'required' => ':attribute wajib diisi.',
            'numeric' => ':attribute harus berupa angka.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'min' => ':attribute tidak boleh negatif.',
            'in' => 'Pilihan :attribute tidak valid.',
            'email' => 'Format email tidak valid.',
            'max' => ':attribute maksimal :max karakter.',
        ], [
            'form.name' => 'Nama Kawasan', 'form.category' => 'Kategori Kawasan',
            'form.total_area' => 'Total Luas Kawasan', 'form.desalination' => 'Desalinasi',
            'form.desalination_flow' => 'Debit Desalinasi', 'form.water_treatment' => 'Water Treatment',
            'form.substations' => 'Gardu Induk', 'form.electricity_number' => 'Electricity Number',
            'form.cable_landing_station' => 'Cable Landing Station', 'form.contact_name' => 'Nama Kontak',
            'form.provider_ids' => 'Provider', 'form.provider_ids.*' => 'Provider',
            'form.email' => 'Email', 'form.phone' => 'Phone',
        ])->validate()['form'];

        $data['desalination'] = (string) $data['desalination'];
        $data['water_treatment'] = (string) $data['water_treatment'];
        if ($data['desalination'] === '0') {
            $data['desalination_flow'] = null;
        }

        return $data;
    }

    public function render(): View
    {
        $search = mb_strtolower(trim($this->search));

        return view('livewire.pages.areas.index', [
            'filteredAreas' => array_filter($this->areas, fn ($area) => $search === ''
                || str_contains(mb_strtolower($area['name']), $search)
                || str_contains(mb_strtolower($area['category']), $search)),
        ])
            ->layout('layouts.app', ['title' => 'Kawasan']);
    }
}
