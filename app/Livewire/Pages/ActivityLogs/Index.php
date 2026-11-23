<?php

namespace App\Livewire\Pages\ActivityLogs;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Index extends Component
{
    #[Locked]
    public array $logs = [];

    public string $search = '';

    public string $activity = '';

    public string $startDate = '';

    public string $endDate = '';

    public function mount(): void
    {
        // Presentation samples only, ordered from newest to oldest.
        $this->logs = [
            ['user' => 'Admin', 'email' => 'admin.monitoring@example.com', 'role' => 'admin', 'activity' => 'Logout', 'description' => 'Keluar dari sistem', 'time' => '18 Nov 2026, 12:15'],
            ['user' => 'Super Admin', 'email' => 'superadmin@example.com', 'role' => 'super_admin', 'activity' => 'Restored', 'description' => 'Memulihkan data perusahaan PT Contoh Digital', 'time' => '18 Nov 2026, 11:45'],
            ['user' => 'Super Admin', 'email' => 'superadmin@example.com', 'role' => 'super_admin', 'activity' => 'Deleted', 'description' => 'Menghapus pengguna Budi Santoso', 'time' => '18 Nov 2026, 11:00'],
            ['user' => 'Admin', 'email' => 'admin.monitoring@example.com', 'role' => 'admin', 'activity' => 'Updated', 'description' => 'Mengubah data pusat data Jakarta Alpha DC', 'time' => '18 Nov 2026, 10:20'],
            ['user' => 'Admin', 'email' => 'admin.monitoring@example.com', 'role' => 'admin', 'activity' => 'Created', 'description' => 'Menambahkan perusahaan PT Contoh Digital', 'time' => '18 Nov 2026, 09:35'],
            ['user' => 'Super Admin', 'email' => 'superadmin@example.com', 'role' => 'super_admin', 'activity' => 'Login', 'description' => 'Berhasil masuk ke sistem', 'time' => '18 Nov 2026, 09:10'],
        ];
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'activity', 'startDate', 'endDate');
    }

    public function render(): View
    {
        $search = mb_strtolower(trim($this->search));
        $filteredLogs = array_filter($this->logs, function (array $log) use ($search): bool {
            $date = CarbonImmutable::createFromFormat('d M Y, H:i', $log['time'], 'Asia/Jakarta')->format('Y-m-d');

            return ($search === '' || str_contains(mb_strtolower($log['user']), $search)
                    || str_contains(mb_strtolower($log['description']), $search))
                && ($this->activity === '' || $log['activity'] === $this->activity)
                && ($this->startDate === '' || $date >= $this->startDate)
                && ($this->endDate === '' || $date <= $this->endDate);
        });

        return view('livewire.pages.activity-logs.index', [
            'filteredLogs' => $filteredLogs,
            'roles' => ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'viewer' => 'Viewer'],
            'hasFilters' => $search !== '' || $this->activity !== '' || $this->startDate !== '' || $this->endDate !== '',
        ])
            ->layout('layouts.app', ['title' => 'Log Aktivitas']);
    }
}
