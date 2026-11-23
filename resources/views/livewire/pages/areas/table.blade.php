<section class="min-w-0 overflow-hidden rounded-xl border border-neutral-300 bg-white" aria-labelledby="area-list-title">
    <div class="flex flex-col gap-4 border-b border-neutral-300 p-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h3 id="area-list-title" class="text-base font-semibold">Daftar Kawasan</h3>
            <p class="mt-1 text-xs text-neutral-700" role="status">{{ count($filteredAreas) }} dari {{ count($areas) }}
                kawasan</p>
        </div>
        <div class="w-full sm:max-w-sm">
            <label for="area-search" class="mb-1 block text-xs font-medium">Cari Kawasan</label>
            <x-search id="area-search" :with-icon="false" wire:model.live.debounce.300ms="search"
                placeholder="Cari nama atau kategori kawasan" />
        </div>
    </div>
    <x-table label="Tabel Kawasan" :framed="false" min-width="min-w-160">
        <x-slot:header>
            <tr>
                <th scope="col" class="w-16 text-center">No</th>
                <th scope="col">Nama Kawasan</th>
                <th scope="col">Kategori Kawasan</th>
                <th scope="col">Luas Kawasan</th>
                <th scope="col" class="text-center">Aksi</th>
            </tr>
        </x-slot:header>
        @forelse ($filteredAreas as $id => $area)
            <tr wire:key="area-{{ $id }}">
                <td class="text-center tabular-nums">{{ $loop->iteration }}</td>
                <td class="min-w-48 font-medium wrap-anywhere">{{ $area['name'] }}</td>
                <td><x-badge :variant="match ($area['category']) {
                    'KEK' => 'primary',
                    'Kawasan Industri' => 'info',
                    default => 'neutral',
                }">{{ $area['category'] }}</x-badge></td>
                <td class="whitespace-nowrap tabular-nums">
                    {{ $area['total_area'] !== null ? number_format((float) $area['total_area'], 2, ',', '.') . ' ha' : '-' }}
                </td>
                <td class="py-2 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <x-button-detail :data-id="$id" :data-name="$area['name']" wire:loading.attr="disabled"
                            ::disabled="modalActive"
                            @click="$dispatch('drawer:open', { name: 'area-detail', id: Number($el.dataset.id) })" />
                        <x-button-edit :data-id="$id" :data-name="$area['name']" wire:loading.attr="disabled"
                            ::disabled="modalActive"
                            @click="$dispatch('modal-edit:open', { name: 'area-edit', id: Number($el.dataset.id), data: { ...$wire.areas[$el.dataset.id] } })" />
                        <x-button-delete :data-id="$id" :data-name="$area['name']" wire:loading.attr="disabled"
                            ::disabled="modalActive"
                            @click="$dispatch('modal-delete:open', { name: 'area-delete', id: Number($el.dataset.id), dataName: $el.dataset.name })" />
                    </div>
                </td>
            </tr>
        @empty
            <tr class="hover:bg-transparent">
                <td colspan="5" class="px-4 py-8 text-center">
                    <p class="font-semibold">
                        {{ trim($search) !== '' ? 'Kawasan tidak ditemukan' : 'Belum ada kawasan' }}</p>
                    <p class="mt-1 text-neutral-700">
                        {{ trim($search) !== '' ? 'Coba nama atau kategori lainnya.' : 'Tambahkan kawasan melalui form di atas.' }}
                    </p>
                </td>
            </tr>
        @endforelse
    </x-table>
</section>
