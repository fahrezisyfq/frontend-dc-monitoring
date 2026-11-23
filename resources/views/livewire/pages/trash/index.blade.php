<div class="space-y-4 [&_button]:transition-colors [&_button]:duration-200 [&_button:disabled]:opacity-50 motion-reduce:[&_button]:transition-none" x-data @trash-completed.window="$nextTick(() => $refs.trashTable.querySelector('[role=region]').focus())">
    <div>
        <h2 class="text-xl font-semibold tracking-tight">Trash</h2>
        <p class="mt-1 text-xs text-neutral-700">Daftar data yang telah dihapus sementara.</p>
    </div>

    <x-toast />

    <section x-ref="trashTable" class="[&_[role=region]:focus-visible]:outline-2 [&_[role=region]:focus-visible]:-outline-offset-2 [&_[role=region]:focus-visible]:outline-primary min-w-0 overflow-hidden rounded-xl border border-neutral-300 bg-white" aria-labelledby="trash-list-title">
        <div class="border-b border-neutral-300 p-4">
            <h3 id="trash-list-title" class="text-base font-semibold">Daftar Data Terhapus</h3>
            <p class="mt-1 text-xs text-neutral-700">Data yang dihapus sementara dapat dipulihkan atau dihapus permanen.</p>
            <p class="mt-1 text-xs text-neutral-700" role="status">{{ count($filteredItems) }} dari {{ count($items) }} data contoh · Simulasi; muat ulang halaman untuk mengembalikan data awal.</p>
            <div class="mt-3 grid grid-cols-1 items-end gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto]">
                <div class="min-w-0">
                    <label for="trash-search" class="mb-1 block text-xs font-medium">Pencarian</label>
                    <x-search id="trash-search" :with-icon="false" wire:model.live.debounce.300ms="search" placeholder="Cari nama data terhapus" />
                </div>
                <div class="min-w-0">
                    <label for="trash-type" class="mb-1 block text-xs font-medium">Jenis Data</label>
                    <select id="trash-type" wire:model.live.change="type" class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                        <option value="">Semua jenis data</option>
                        @foreach (['Perusahaan', 'Data Center', 'Pengguna'] as $dataType)
                            <option value="{{ $dataType }}">{{ $dataType }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-0">
                    <label for="trash-start-date" class="mb-1 block text-xs font-medium">Tanggal awal</label>
                    <input id="trash-start-date" type="date" wire:model.live.change="startDate" class="min-h-10 sm:min-h-9 w-full min-w-0 rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                </div>
                <div class="min-w-0">
                    <label for="trash-end-date" class="mb-1 block text-xs font-medium">Tanggal akhir</label>
                    <input id="trash-end-date" type="date" wire:model.live.change="endDate" class="min-h-10 sm:min-h-9 w-full min-w-0 rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                </div>
                <x-button variant="secondary" wire:click="resetFilters" wire:loading.attr="disabled" class="py-0! text-neutral-700! bg-transparent! ease-in-out! enabled:active:opacity-100! disabled:cursor-default! disabled:hover:bg-neutral-100">
                    {{-- Lucide: rotate-ccw --}}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><path d="M3 11a9 9 0 1 1 2.7 6.4M3 3v8h8" /></svg>
                    Reset
                </x-button>
            </div>
            @if ($startDate !== '' && $endDate !== '' && $startDate > $endDate)
                <p class="mt-2 text-xs text-red" role="alert">Tanggal akhir harus sama dengan atau setelah tanggal awal.</p>
            @endif
        </div>
        @include('livewire.pages.trash.table')
    </section>

    @include('livewire.pages.trash.modals')
</div>
