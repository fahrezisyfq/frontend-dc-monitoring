<section class="min-w-0 overflow-hidden rounded-xl border border-neutral-300 bg-white" aria-labelledby="service-list-title">
    <div class="flex flex-col gap-4 border-b border-neutral-300 p-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h3 id="service-list-title" class="text-base font-semibold">Daftar Service</h3>
            <p class="mt-1 text-xs text-neutral-700" role="status">{{ count($filteredServices) }} dari {{ count($services) }} service</p>
        </div>
        <div class="w-full sm:max-w-sm">
            <label for="service-search" class="mb-1 block text-xs font-medium">Cari Service</label>
            <x-search id="service-search" :with-icon="false" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau description Service" />
        </div>
    </div>
    <x-table label="Tabel Service" :framed="false" min-width="min-w-160">
        <x-slot:header>
            <tr>
                <th scope="col" class="w-16 text-center">No</th>
                <th scope="col">Nama Service</th>
                <th scope="col">Description</th>
                <th scope="col" class="text-center">Aksi</th>
            </tr>
        </x-slot:header>
        @forelse ($filteredServices as $id => $service)
            <tr wire:key="service-{{ $id }}">
                <td class="text-center tabular-nums">{{ $loop->iteration }}</td>
                <td class="min-w-48 font-medium wrap-anywhere">{{ $service['name'] }}</td>
                <td class="max-w-96 text-neutral-700 wrap-anywhere">
                    <p class="line-clamp-2" title="{{ $service['description'] }}">{{ $service['description'] !== '' ? $service['description'] : '—' }}</p>
                </td>
                <td class="py-2 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <x-button-edit :data-id="$id" :data-name="$service['name']" wire:loading.attr="disabled" ::disabled="modalActive"
                            @click="$dispatch('modal-edit:open', { name: 'service-edit', id: Number($el.dataset.id), data: { ...$wire.services[$el.dataset.id] } })" />
                        <x-button-delete :data-id="$id" :data-name="$service['name']" wire:loading.attr="disabled" ::disabled="modalActive"
                            @click="$dispatch('modal-delete:open', { name: 'service-delete', id: Number($el.dataset.id), dataName: $el.dataset.name })" />
                    </div>
                </td>
            </tr>
        @empty
            <tr class="hover:bg-transparent">
                <td colspan="4" class="px-4 py-8 text-center">
                    <p class="font-semibold">{{ trim($search) !== '' ? 'Service tidak ditemukan' : 'Belum ada Service' }}</p>
                    <p class="mt-1 text-neutral-700">{{ trim($search) !== '' ? 'Coba nama atau description lainnya.' : 'Tambahkan Service melalui form di atas.' }}</p>
                </td>
            </tr>
        @endforelse
    </x-table>
</section>
