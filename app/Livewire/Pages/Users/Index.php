<?php

namespace App\Livewire\Pages\Users;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Index extends Component
{
    public const ROLES = ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'viewer' => 'Viewer'];

    public const FIELDS = ['name', 'email', 'role', 'password', 'confirmation'];

    #[Locked]
    public array $users = [];

    #[Locked]
    public int $nextId = 7;

    public string $search = '';

    public array $form = ['name' => '', 'email' => '', 'role' => '', 'password' => '', 'confirmation' => ''];

    public function mount(): void
    {
        $samples = [
            ['Administrator Sistem', 'superadmin@example.com', 'super_admin', '2026-11-17T10:30:00+07:00'],
            ['Admin Infrastruktur', 'admin.infrastruktur@example.com', 'admin', '2026-11-16T14:25:00+07:00'],
            ['Admin Monitoring', 'admin.monitoring@example.com', 'admin', '2026-11-16T09:15:00+07:00'],
            ['Pengamat Data', 'viewer.data@example.com', 'viewer', '2026-11-15T16:45:00+07:00'],
            ['Pengamat Operasional', 'viewer.operasional@example.com', 'viewer', null],
            ['Koordinator Sistem', 'koordinator@example.com', 'super_admin', '2026-11-14T08:10:00+07:00'],
        ];

        foreach ($samples as $index => [$name, $email, $role, $lastActivity]) {
            $this->users[$index + 1] = [...compact('name', 'email', 'role'), 'last_activity' => $lastActivity];
        }
    }

    public function resetForm(): void
    {
        $this->form = array_fill_keys(self::FIELDS, '');
        $this->resetValidation(array_map(fn (string $field): string => 'form.'.$field, self::FIELDS));
    }

    public function createUser(): void
    {
        $this->form = $this->trimDraft($this->form);
        $validated = $this->validate($this->validationRules(null, true), $this->validationMessages(), $this->validationAttributes());

        // Passwords are used only for form validation, never for the dummy list.
        $this->users[$this->nextId++] = [
            ...array_intersect_key($validated['form'], array_flip(['name', 'email', 'role'])),
            'last_activity' => null,
        ];
        $this->search = '';
        $this->resetForm();
        $this->dispatch('toast', type: 'success', message: 'Pengguna berhasil ditambahkan ke simulasi.');
    }

    public function saveUser(array $draft, ?int $id = null): array
    {
        if ($id !== null && ! isset($this->users[$id])) {
            return ['success' => false, 'message' => 'Pengguna tidak ditemukan. Silakan buka kembali modal.'];
        }

        $draft = $this->trimDraft(array_intersect_key($draft, array_flip(self::FIELDS)));
        $passwordRequired = $id === null
            || filled($draft['password'] ?? null) || filled($draft['confirmation'] ?? null);
        $validator = Validator::make(
            ['form' => $draft],
            $this->validationRules($id, $passwordRequired),
            $this->validationMessages(),
            $this->validationAttributes(),
        );

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->toArray()];
        }

        $data = $validator->validated()['form'];
        $creating = $id === null;
        $id ??= $this->nextId++;
        $this->users[$id] = [
            ...array_intersect_key($data, array_flip(['name', 'email', 'role'])),
            'last_activity' => $this->users[$id]['last_activity'] ?? null,
        ];
        if ($creating) {
            $this->search = '';
        }
        $this->dispatch('toast', type: 'success', message: $creating
            ? 'Pengguna berhasil ditambahkan ke simulasi.'
            : 'Pengguna berhasil diperbarui dalam simulasi.');

        return ['success' => true];
    }

    public function deleteUser(int $id): array
    {
        if (! isset($this->users[$id])) {
            return ['success' => false, 'message' => 'Pengguna tidak ditemukan. Silakan buka kembali modal.'];
        }

        unset($this->users[$id]);
        $this->dispatch('toast', type: 'success', message: 'Pengguna berhasil dihapus dari simulasi.');

        return ['success' => true];
    }

    public function render(): View
    {
        $search = mb_strtolower(trim($this->search));

        return view('livewire.pages.users.index', [
            'filteredUsers' => array_filter($this->users, fn ($user) => $search === ''
                || str_contains(mb_strtolower($user['name']), $search)
                || str_contains(mb_strtolower($user['email']), $search)),
            'roles' => self::ROLES,
        ])
            ->layout('layouts.app', ['title' => 'Pengguna']);
    }

    private function validationRules(?int $id, bool $passwordRequired): array
    {
        $others = $id === null ? $this->users : collect($this->users)->except($id)->all();
        $otherEmails = collect($others)->pluck('email')->map(fn ($email) => mb_strtolower($email))->all();

        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => ['bail', 'required', 'string', 'max:255', 'email', function ($attribute, $value, $fail) use ($otherEmails) {
                if (in_array(mb_strtolower($value), $otherEmails, true)) {
                    $fail('Email sudah digunakan dalam daftar pengguna.');
                }
            }],
            'form.role' => ['required', Rule::in(array_keys(self::ROLES))],
            'form.password' => [$passwordRequired ? 'required' : 'nullable', 'string'],
            'form.confirmation' => [$passwordRequired ? 'required' : 'nullable', 'string', 'same:form.password'],
        ];
    }

    private function validationMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
            'email' => 'Masukkan alamat email yang valid.',
            'in' => 'Pilih role pengguna.',
            'same' => 'Konfirmasi password tidak cocok.',
        ];
    }

    private function validationAttributes(): array
    {
        return [
            'form.name' => 'Nama pengguna',
            'form.email' => 'Email',
            'form.role' => 'Role',
            'form.password' => 'Password',
            'form.confirmation' => 'Konfirmasi password',
        ];
    }

    private function trimDraft(array $draft): array
    {
        foreach (['name', 'email'] as $field) {
            if (is_string($draft[$field] ?? null)) {
                $draft[$field] = trim($draft[$field]);
            }
        }

        return $draft;
    }
}
