<x-table label="Tabel data terhapus" :framed="false">
    <x-slot:header>
        <tr>
            <th scope="col">No.</th>
            <th scope="col">Jenis Data</th>
            <th scope="col">Nama Data</th>
            <th scope="col">Dihapus Oleh</th>
            <th scope="col">Tanggal Dihapus</th>
            <th scope="col" class="text-center">Aksi</th>
        </tr>
    </x-slot:header>
    @forelse ($filteredItems as $item)
        <tr wire:key="trash-{{ $item['id'] }}" class="ease-in-out">
            <td class="tabular-nums">{{ $loop->iteration }}</td>
            <td><x-badge :variant="match ($item['type']) {
                'Perusahaan' => 'primary',
                'Data Center' => 'info',
                default => 'neutral',
            }">{{ $item['type'] }}</x-badge></td>
            <td class="min-w-48 max-w-72 font-medium wrap-anywhere">{{ $item['name'] }}</td>
            <td class="min-w-56 max-w-72 wrap-anywhere">
                <div class="flex flex-col items-start gap-0.5">
                    <span class="text-sm font-medium leading-5">{{ $item['deleted_by'] }}</span>
                    <span class="text-xs leading-4 text-neutral-700">{{ $item['deleted_by_email'] }}</span>
                    <x-badge :variant="match ($item['deleted_by_role']) {
                        'super_admin' => 'purple',
                        'admin' => 'primary',
                        default => 'neutral',
                    }">{{ $roles[$item['deleted_by_role']] }}</x-badge>
                </div>
            </td>
            <td class="whitespace-nowrap text-neutral-700 tabular-nums">{{ $item['deleted_at'] }} WIB</td>
            <td class="py-2 text-center">
                <div class="flex items-center justify-center gap-1">
                    @foreach (['restore' => 'Pulihkan', 'delete' => 'Hapus Permanen'] as $action => $label)
                        <button type="button" wire:click="openModal({{ $item['id'] }}, '{{ $action }}')"
                            wire:loading.attr="disabled" title="{{ $label }}"
                            aria-label="{{ $label }} {{ $item['name'] }}" @class([
                                'inline-flex size-11 shrink-0 items-center justify-center rounded-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary sm:size-9',
                                'text-primary hover:bg-primary/5' => $action === 'restore',
                                'text-red hover:bg-red/5' => $action === 'delete',
                            ])>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true">
                                @if ($action === 'restore')
                                    {{-- Lucide: rotate-ccw --}}
                                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8M3 3v5h5" />
                                @else
                                    {{-- Lucide: trash-2 --}}
                                    <path
                                        d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2M10 11v6M14 11v6" />
                                @endif
                            </svg>
                        </button>
                    @endforeach
                </div>
            </td>
        </tr>
    @empty
        <tr class="hover:bg-transparent">
            <td colspan="6" class="px-4 py-8 text-center">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" class="mx-auto mb-2 size-6 text-neutral-700" aria-hidden="true">
                    <path
                        d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2M10 11v6M14 11v6" />
                </svg>
                <p class="font-semibold">{{ $hasFilters ? 'Data tidak ditemukan' : 'Tidak ada data terhapus' }}</p>
                <p class="mt-1 text-neutral-700">
                    {{ $hasFilters ? 'Tidak ada data yang sesuai dengan filter. Ubah pencarian, jenis data, atau rentang tanggal, atau gunakan tombol Reset.' : 'Semua data contoh telah dipulihkan atau dihapus dalam simulasi.' }}
                </p>
            </td>
        </tr>
    @endforelse
</x-table>
