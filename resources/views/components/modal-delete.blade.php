@props([
    'name' => 'delete',
    'action' => null,
    'title' => 'Hapus Data',
    'message' => 'Apakah Anda yakin ingin menghapus data ini?',
    'dataName' => '',
    'dataId' => null,
    'showClose' => false,
    'transition' => true,
    'busyLabel' => 'Menghapus…',
    'closeOnBackdrop' => true,
])

{{-- Place inside the owning Livewire component. Use a unique name for each modal on a page.
    <x-modal-delete name="records" action="removeRecord" />
    Open: $dispatch('modal-delete:open', { name: 'records', id: recordId, dataName: recordName })
    The action must return { success: true }; see modal-delete.js for the response contract.
--}}
<div x-data="modalDelete(@js(['name' => $name, 'action' => $action, 'title' => $title, 'message' => $message, 'dataName' => $dataName, 'dataId' => $dataId]))" x-id="['modal-delete-title', 'modal-delete-description']"
    x-on:modal-delete:open.window="openModal($event.detail)" x-on:keydown.escape.window="closeModal()"
    @if ($closeOnBackdrop) x-on:click.self="closeModal()" @endif x-cloak x-show="isOpen"
    x-trap.inert.noscroll="isOpen"
    @if ($transition) x-transition:enter="transition-opacity duration-200 ease-out motion-reduce:transition-none"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity duration-200 ease-in motion-reduce:transition-none"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @endif
    role="dialog" aria-modal="true" :aria-labelledby="$id('modal-delete-title')"
    :aria-describedby="$id('modal-delete-description')" :aria-busy="busy"
    {{ $attributes->class(['fixed inset-0 z-60 flex items-center justify-center bg-neutral-900/40 p-4 text-neutral-900']) }}>
    <div x-show="isOpen"
        @if ($transition) x-transition:enter="transition-[opacity,transform] duration-200 ease-out motion-reduce:transition-none"
        x-transition:enter-start="opacity-0 scale-98 motion-reduce:scale-100" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition-[opacity,transform] duration-200 ease-in motion-reduce:transition-none"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-98 motion-reduce:scale-100" @endif
        class="max-h-[90dvh] w-full max-w-xl overflow-y-auto rounded-xl border border-neutral-300 bg-white shadow-lg">
        <div @class([
            'border-b border-neutral-300 px-4 py-3',
            'flex items-center justify-between gap-4' => $showClose,
        ])>
            <h3 :id="$id('modal-delete-title')" class="text-base font-semibold wrap-anywhere" x-text="title"></h3>
            @if ($showClose)
                <button type="button" @click="closeModal()" :disabled="busy" aria-label="Tutup dialog"
                    class="flex size-11 shrink-0 items-center justify-center rounded-lg text-neutral-700 hover:bg-neutral-100 focus-visible:outline-2 focus-visible:outline-primary sm:size-9">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" class="size-4" aria-hidden="true">
                        <path d="m18 6-12 12M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
        <p x-cloak x-show="error" x-text="error" role="alert"
            class="mx-4 mt-3 rounded-lg bg-red/5 p-3 text-sm text-red wrap-anywhere"></p>
        <div :id="$id('modal-delete-description')" class="space-y-2 p-4 text-sm">
            @isset($confirmation)
                {{ $confirmation }}
            @else
                <p class="wrap-anywhere" x-text="message"></p>
                <p x-show="dataName" class="font-semibold wrap-anywhere" x-text="dataName"></p>
            @endisset
            {{ $slot }}
        </div>
        <div class="flex justify-end gap-2 border-t border-neutral-300 px-4 py-3">
            <button type="button" @click="closeModal()" :disabled="busy"
                class="min-h-11 rounded-lg border border-neutral-300 px-3 text-sm font-medium hover:bg-neutral-100 focus-visible:outline-2 focus-visible:outline-primary disabled:opacity-50 sm:min-h-9">Batal</button>
            <button type="button" @click="confirmDelete()" :disabled="busy"
                class="min-h-11 rounded-lg bg-red px-3 text-sm font-medium text-white hover:bg-red/90 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red disabled:opacity-50 sm:min-h-9">
                <span x-show="!busy">Hapus</span>
                <span x-cloak x-show="busy" role="status">{{ $busyLabel }}</span>
            </button>
        </div>
    </div>
</div>
