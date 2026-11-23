<x-table label="Tabel log aktivitas" :framed="false">
    <x-slot:header>
        <tr>
            <th scope="col">No.</th>
            <th scope="col">Pengguna</th>
            <th scope="col">Aktivitas</th>
            <th scope="col">Deskripsi</th>
            <th scope="col">Waktu</th>
        </tr>
    </x-slot:header>
    @forelse ($filteredLogs as $id => $log)
        <tr wire:key="activity-log-{{ $id }}" class="ease-in-out">
            <td class="tabular-nums">{{ $loop->iteration }}</td>
            <td class="min-w-40 max-w-60 wrap-anywhere">
                <div class="flex flex-col items-start gap-0.5">
                    <span class="text-sm font-medium leading-5">{{ $log['user'] }}</span>
                    <span class="text-xs leading-4 text-neutral-700">{{ $log['email'] }}</span>
                    <x-badge :variant="match ($log['role']) { 'super_admin' => 'purple', 'admin' => 'primary', default => 'neutral' }">{{ $roles[$log['role']] }}</x-badge>
                </div>
            </td>
            <td><x-badge :variant="match (strtolower($log['activity'])) { 'created', 'restored' => 'success', 'updated' => 'info', 'deleted' => 'danger', 'login' => 'primary', default => 'neutral' }">{{ $log['activity'] }}</x-badge></td>
            <td class="min-w-64 max-w-96 text-neutral-700 wrap-anywhere">{{ $log['description'] }}</td>
            <td class="whitespace-nowrap text-neutral-700 tabular-nums">{{ $log['time'] }} WIB</td>
        </tr>
    @empty
        <tr class="hover:bg-transparent">
            <td colspan="5" class="px-4 py-8 text-center">
                {{-- Lucide: history --}}
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2 size-6 text-neutral-700" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8M3 3v5h5M12 7v5l4 2" /></svg>
                <p class="font-semibold">{{ $hasFilters ? 'Aktivitas tidak ditemukan' : 'Belum ada aktivitas' }}</p>
                <p class="mt-1 text-neutral-700">{{ $hasFilters ? 'Tidak ada aktivitas yang sesuai dengan filter. Ubah pencarian, jenis aktivitas, atau rentang tanggal, atau gunakan tombol Reset.' : 'Daftar log aktivitas masih kosong.' }}</p>
            </td>
        </tr>
    @endforelse
</x-table>
