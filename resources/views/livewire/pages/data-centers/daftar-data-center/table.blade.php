<section class="min-w-0 overflow-hidden rounded-xl border border-neutral-300 bg-white"
    aria-labelledby="data-center-list-title">
    <div class="border-b border-neutral-300 p-4">
        <h3 id="data-center-list-title" class="text-base font-semibold">Daftar pusat data</h3>
        <div class="relative mt-1 text-xs text-neutral-700">
            <p wire:loading.delay.class="invisible" wire:target="search, company, status, province">
                {{ count($filteredDataCenters) }} dari {{ count($dataCenters) }} pusat data</p>
            <p wire:loading.delay wire:target="search, company, status, province" role="status"
                class="absolute inset-0 whitespace-nowrap">
                Memfilter pusat data…</p>
        </div>
        <div class="mt-3 overflow-x-auto" tabindex="0" role="region"
            aria-label="Filter pusat data, gulir horizontal pada layar kecil">
            <div
                class="grid min-w-[560px] grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,0.8fr)_minmax(0,0.8fr)_auto] items-end gap-2">
                <div class="min-w-0">
                    <label for="data-center-search" class="mb-1 block text-xs font-medium">Cari pusat data</label>
                    <x-search id="data-center-search" wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama, perusahaan, lokasi, atau status" aria-label="Cari pusat data" />
                </div>
                <div class="min-w-0">
                    <label for="data-center-company" class="mb-1 block text-xs font-medium">Perusahaan</label>
                    <select id="data-center-company" wire:model.live.change="company"
                        class="min-h-10 w-full min-w-0 rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                        <option value="">Semua perusahaan</option>
                        @foreach ($companies as $companyId => $companyOption)
                            <option value="{{ $companyId }}">{{ $companyOption['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-0">
                    <label for="data-center-status" class="mb-1 block text-xs font-medium">Status</label>
                    <select id="data-center-status" wire:model.live.change="status"
                        class="min-h-10 w-full min-w-0 rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                        <option value="">Semua status</option>
                        @foreach ($statuses as $statusOption)
                            <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-0">
                    <label for="data-center-province" class="mb-1 block text-xs font-medium">Lokasi</label>
                    <select id="data-center-province" wire:model.live.change="province"
                        class="min-h-10 w-full min-w-0 rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                        <option value="">Semua lokasi</option>
                        @foreach ($provinces as $provinceOption)
                            <option value="{{ $provinceOption }}">{{ $provinceOption }}</option>
                        @endforeach
                    </select>
                </div>
                <x-button variant="secondary" wire:click="resetFilters" wire:loading.attr="disabled"
                    class="py-0! text-neutral-700! bg-transparent! ease-in-out! enabled:active:opacity-100! disabled:cursor-default! disabled:hover:bg-neutral-100">
                    {{-- Lucide: rotate-ccw --}}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true">
                        <path d="M3 11a9 9 0 1 1 2.7 6.4M3 3v8h8" />
                    </svg>
                    Reset
                </x-button>
            </div>
        </div>
    </div>
    <x-table label="Tabel pusat data" :framed="false">
        <x-slot:header>
            <tr>
                <th scope="col" class="w-16 whitespace-nowrap">No.</th>
                <th scope="col" class="whitespace-nowrap">Nama Pusat Data</th>
                <th scope="col" class="whitespace-nowrap">Perusahaan</th>
                <th scope="col" class="whitespace-nowrap">Lokasi</th>
                <th scope="col" class="whitespace-nowrap">Status</th>
                <th scope="col" class="whitespace-nowrap text-right">IT Load Design</th>
                <th scope="col" class="whitespace-nowrap text-center">Aksi</th>
            </tr>
        </x-slot:header>
        <x-slot:body wire:loading.delay.class="opacity-75" wire:target="search, company, status, province"
            class="transition-opacity duration-200 ease-out motion-reduce:transition-none">
            @forelse ($filteredDataCenters as $id => $center)
                @php($form = $center['form'])
                <tr wire:key="data-center-{{ $id }}">
                    <td class="tabular-nums">{{ $loop->iteration }}</td>
                    <td class="min-w-48 font-medium">{{ $form['name'] }}</td>
                    <td>
                        <p class="font-medium">{{ $center['company_name'] }}</p>
                        <p class="mt-0.5 whitespace-nowrap text-xs text-neutral-700 tabular-nums">
                            {{ $center['company_nib'] }}</p>
                    </td>
                    <td class="min-w-44 text-neutral-700">
                        <p class="wrap-anywhere">{{ $form['city'] }}</p>
                        <p class="mt-0.5 text-xs">{{ $form['province'] }}</p>
                    </td>
                    <td>
                        <x-badge :variant="match ($form['status']) {
                            'Operational' => 'primary',
                            'Construction' => 'info',
                            'Planning' => 'warning',
                            default => 'neutral',
                        }" class="whitespace-normal!">{{ $form['status'] }}</x-badge>
                    </td>
                    <td class="whitespace-nowrap text-right tabular-nums">
                        {{ $form['design_load'] !== '' ? $form['design_load'] . ' kW' : '—' }}</td>
                    <td class="w-40 py-2">
                        <div class="flex min-w-35 items-center justify-center gap-1 sm:min-w-29">
                            <x-button-detail :data-id="$id" :data-name="$form['name']" ::disabled="modalActive"
                                @click="$dispatch('drawer:open', { name: 'data-center-detail', id: Number($el.dataset.id) })" />
                            <x-button-edit :data-id="$id" :data-name="$form['name']" ::disabled="modalActive"
                                @click="$dispatch('modal-edit:open', { name: 'data-center-edit', id: Number($el.dataset.id) })" />
                            <x-button-delete :data-id="$id" :data-name="$form['name']" ::disabled="modalActive"
                                @click="$dispatch('modal-delete:open', { name: 'data-center-delete', id: Number($el.dataset.id), dataName: $el.dataset.name })" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="7" class="px-4 py-8 text-center">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="mx-auto mb-2 size-6 text-neutral-700" aria-hidden="true">
                            <rect x="4" y="3" width="16" height="7" rx="2" />
                            <rect x="4" y="14" width="16" height="7" rx="2" />
                            <path d="M8 6.5h.01M8 17.5h.01M12 6.5h4M12 17.5h4" />
                        </svg>
                        <p class="font-semibold">
                            {{ $hasFilters ? 'Pusat data tidak ditemukan' : 'Belum ada pusat data' }}</p>
                        <p class="mt-1 text-neutral-700">
                            {{ $hasFilters ? 'Coba ubah kata kunci atau filter yang dipilih.' : 'Tambahkan pusat data untuk memulai simulasi.' }}
                        </p>
                        @if ($hasFilters)
                            <x-button variant="ghost" wire:click="resetFilters"
                                class="mt-3 py-0! focus-visible:outline-offset-0!">Reset filter</x-button>
                        @endif
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-table>
</section>
