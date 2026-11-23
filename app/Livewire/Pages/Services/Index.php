<?php

namespace App\Livewire\Pages\Services;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Index extends Component
{
    #[Locked]
    public array $services = [];

    #[Locked]
    public int $nextId = 7;

    public string $search = '';

    public array $form = ['name' => '', 'description' => ''];

    public function mount(): void
    {
        $this->services = [
            1 => ['name' => 'Rack Colocation', 'description' => 'Penyewaan ruang rack untuk penempatan perangkat pelanggan.'],
            2 => ['name' => 'Caged Colocation', 'description' => 'Area rack berpagar dengan akses khusus untuk pelanggan.'],
            3 => ['name' => 'Private Suite', 'description' => 'Ruang privat untuk kebutuhan infrastruktur pelanggan.'],
            4 => ['name' => 'Dedicated Data Hall', 'description' => 'Satu ruang data hall yang didedikasikan untuk pelanggan.'],
            5 => ['name' => 'Dedicated Building', 'description' => 'Gedung pusat data khusus untuk kebutuhan satu pelanggan.'],
            6 => ['name' => 'Server Provision', 'description' => 'Penyediaan perangkat server sesuai kebutuhan komputasi.'],
        ];
    }

    public function resetForm(): void
    {
        $this->form = ['name' => '', 'description' => ''];
        $this->resetValidation(['form.name', 'form.description']);
    }

    public function createService(): void
    {
        $data = $this->validatedForm('form');
        $this->services[$this->nextId++] = $data;
        $this->resetForm();
        $this->dispatch('toast', type: 'success', message: 'Service berhasil ditambahkan dalam simulasi.');
    }

    public function saveService(array $draft, int $id): array
    {
        if (! isset($this->services[$id])) {
            return ['success' => false, 'message' => 'Service tidak ditemukan. Buka kembali modal edit.'];
        }

        foreach (['name', 'description'] as $field) {
            if (is_string($draft[$field] ?? null)) {
                $draft[$field] = trim($draft[$field]);
            }
        }
        $validator = Validator::make(['form' => $draft], [
            'form.name' => ['required', 'string', 'max:255'],
            'form.description' => ['nullable', 'string', 'max:2000'],
        ], [
            'form.name.required' => 'Nama Service wajib diisi.',
            'form.name.max' => 'Nama Service maksimal 255 karakter.',
            'form.description.max' => 'Description maksimal 2000 karakter.',
        ]);
        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->toArray()];
        }

        $data = $validator->validated()['form'];
        $this->services[$id] = ['name' => $data['name'], 'description' => $data['description'] ?? ''];
        $this->dispatch('toast', type: 'success', message: 'Service berhasil diperbarui dalam simulasi.');

        return ['success' => true];
    }

    public function deleteService(int $id): array
    {
        if (! isset($this->services[$id])) {
            return ['success' => false, 'message' => 'Service tidak ditemukan.'];
        }

        unset($this->services[$id]);
        $this->dispatch('toast', type: 'success', message: 'Service berhasil dihapus dalam simulasi.');

        return ['success' => true];
    }

    private function validatedForm(string $property): array
    {
        foreach (['name', 'description'] as $field) {
            if (is_string($this->{$property}[$field] ?? null)) {
                $this->{$property}[$field] = trim($this->{$property}[$field]);
            }
        }

        $validated = $this->validate([
            $property.'.name' => ['required', 'string', 'max:255'],
            $property.'.description' => ['nullable', 'string', 'max:2000'],
        ], [
            $property.'.name.required' => 'Nama Service wajib diisi.',
            $property.'.name.max' => 'Nama Service maksimal 255 karakter.',
            $property.'.description.max' => 'Description maksimal 2000 karakter.',
        ]);

        return ['name' => $validated[$property]['name'], 'description' => $validated[$property]['description'] ?? ''];
    }

    public function render(): View
    {
        $search = mb_strtolower(trim($this->search));

        return view('livewire.pages.services.index', [
            'filteredServices' => array_filter($this->services, fn ($service) => $search === ''
                || str_contains(mb_strtolower($service['name']), $search)
                || str_contains(mb_strtolower($service['description']), $search)),
        ])
            ->layout('layouts.app', ['title' => 'Service']);
    }
}
