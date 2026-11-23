<?php

namespace App\Livewire\Pages\Trash;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Index extends Component
{
    #[Locked]
    public array $items = [];

    public string $search = '';

    public string $type = '';

    public string $startDate = '';

    public string $endDate = '';

    #[Locked]
    public ?string $modal = null;

    #[Locked]
    public ?int $selectedId = null;

    public function mount(): void
    {
        // Temporary presentation data; reloading restores the original list.
        $samples = [
            ['Perusahaan', 'PT Contoh Digital', 'Super Admin', '18 Nov 2026, 11:45'],
            ['Pengguna', 'Budi Santoso', 'Super Admin', '18 Nov 2026, 11:00'],
            ['Data Center', 'Jakarta Alpha DC', 'Admin Infrastruktur', '18 Nov 2026, 10:20'],
            ['Perusahaan', 'PT Nusantara Infrastruktur Teknologi', 'Admin', '17 Nov 2026, 15:30'],
            ['Data Center', 'Bandung Digital Park — Gedung Operasional Utama', 'Admin Infrastruktur', '17 Nov 2026, 14:10'],
            ['Pengguna', 'Sari Wulandari', 'Super Admin', '17 Nov 2026, 09:15'],
            ['Perusahaan', 'PT Cakrawala Data Indonesia', 'Admin', '16 Nov 2026, 16:40'],
            ['Data Center', 'Surabaya Beta DC', 'Admin Infrastruktur', '16 Nov 2026, 08:25'],
        ];

        $deletedByUsers = [
            'Super Admin' => ['email' => 'superadmin@example.com', 'role' => 'super_admin'],
            'Admin Infrastruktur' => ['email' => 'admin.infrastruktur@example.com', 'role' => 'admin'],
            'Admin' => ['email' => 'admin.monitoring@example.com', 'role' => 'admin'],
        ];

        foreach ($samples as $index => [$type, $name, $deletedBy, $deletedAt]) {
            $id = $index + 1;
            $this->items[$id] = [
                'id' => $id,
                'type' => $type,
                'name' => $name,
                'deleted_by' => $deletedBy,
                'deleted_by_email' => $deletedByUsers[$deletedBy]['email'],
                'deleted_by_role' => $deletedByUsers[$deletedBy]['role'],
                'deleted_at' => $deletedAt,
            ];
        }
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'type', 'startDate', 'endDate');
    }

    public function openModal(int $id, string $action): void
    {
        $this->closeModal();

        if (! isset($this->items[$id]) || ! in_array($action, ['restore', 'delete'], true)) {
            return;
        }

        $this->selectedId = $id;
        $this->modal = $action;
    }

    public function closeModal(): void
    {
        $this->reset('modal', 'selectedId');
    }

    public function confirm(): void
    {
        if (! in_array($this->modal, ['restore', 'delete'], true) || ! isset($this->items[$this->selectedId])) {
            return;
        }

        $message = $this->modal === 'restore'
            ? 'Data berhasil dipulihkan dalam simulasi.'
            : 'Data berhasil dihapus permanen dalam simulasi.';

        unset($this->items[$this->selectedId]);
        $this->closeModal();
        $this->dispatch('toast', type: 'success', message: $message);
        $this->dispatch('trash-completed');
    }

    public function render(): View
    {
        $search = mb_strtolower(trim($this->search));
        $filteredItems = array_filter($this->items, function (array $item) use ($search): bool {
            $date = CarbonImmutable::createFromFormat('d M Y, H:i', $item['deleted_at'], 'Asia/Jakarta')->format('Y-m-d');

            return ($search === '' || str_contains(mb_strtolower($item['name']), $search))
                && ($this->type === '' || $item['type'] === $this->type)
                && ($this->startDate === '' || $date >= $this->startDate)
                && ($this->endDate === '' || $date <= $this->endDate);
        });

        return view('livewire.pages.trash.index', [
            'filteredItems' => $filteredItems,
            'hasFilters' => $search !== '' || $this->type !== '' || $this->startDate !== '' || $this->endDate !== '',
            'selectedItem' => $this->items[$this->selectedId] ?? null,
            'roles' => ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'viewer' => 'Viewer'],
        ])
            ->layout('layouts.app', ['title' => 'Trash']);
    }
}
