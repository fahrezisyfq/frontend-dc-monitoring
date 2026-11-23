<div wire:key="trash-modal" x-cloak x-show="$wire.modal !== null" x-trap.inert.noscroll="$wire.modal !== null" @keydown.escape.window="if ($wire.modal !== null) $wire.closeModal()" @click.self="$wire.closeModal()" class="fixed inset-0 z-60 flex items-center justify-center bg-neutral-900/40 p-4" role="dialog" aria-modal="true" aria-labelledby="trash-modal-title" aria-describedby="trash-modal-description">
    <div class="max-h-[90dvh] w-full max-w-xl overflow-y-auto rounded-xl border border-neutral-300 bg-white shadow-lg">
        <div class="flex items-center justify-between gap-4 border-b border-neutral-300 px-4 py-3">
            <h3 id="trash-modal-title" class="text-base font-semibold">{{ $modal === 'delete' ? 'Hapus Permanen' : 'Pulihkan Data' }}</h3>
            <button type="button" wire:click="closeModal" wire:loading.attr="disabled" aria-label="Tutup dialog" class="flex size-11 shrink-0 items-center justify-center rounded-lg text-neutral-700 hover:bg-neutral-100 focus-visible:outline-2 focus-visible:outline-primary sm:size-9">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" class="size-4" aria-hidden="true"><path d="m18 6-12 12M6 6l12 12" /></svg>
            </button>
        </div>
        <div id="trash-modal-description" class="space-y-2 p-4 text-sm">
            <p>{{ $modal === 'delete' ? 'Hapus permanen' : 'Pulihkan' }} <strong class="wrap-anywhere">{{ $selectedItem['name'] ?? '' }}</strong>?</p>
            <p class="text-neutral-700">{{ $modal === 'delete' ? 'Penghapusan permanen berarti data tidak dapat dipulihkan kembali.' : 'Data akan dikeluarkan dari daftar Trash sebagai simulasi pemulihan.' }}</p>
            <p class="text-neutral-700">Ini hanya simulasi data contoh. Tidak ada data sungguhan yang berubah. Muat ulang halaman untuk mengembalikan daftar awal.</p>
        </div>
        <div class="flex justify-end gap-2 border-t border-neutral-300 px-4 py-3">
            <x-button variant="secondary" wire:click="closeModal" wire:loading.attr="disabled" class="py-0! bg-transparent! focus-visible:outline-offset-0! ease-in-out! enabled:active:opacity-100! disabled:cursor-default! disabled:hover:bg-neutral-100">Batal</x-button>
            <x-button :variant="$modal === 'delete' ? 'danger' : 'primary'" wire:click="confirm" wire:loading.attr="disabled" :class="'py-0! ease-in-out! enabled:active:opacity-100! disabled:cursor-default! '.($modal === 'delete' ? 'disabled:hover:bg-red/90' : 'disabled:hover:bg-primary/90')">
                <span wire:loading.remove wire:target="confirm">{{ $modal === 'delete' ? 'Hapus Permanen' : 'Pulihkan' }}</span>
                <span wire:loading wire:target="confirm">Memproses...</span>
            </x-button>
        </div>
    </div>
</div>
