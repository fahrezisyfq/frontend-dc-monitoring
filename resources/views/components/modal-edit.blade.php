@props([
    'name' => 'edit',
    'action' => null,
    'loadAction' => null,
    'title' => 'Edit Data',
    'description' => '',
    'cancelLabel' => 'Batal',
    'saveLabel' => 'Simpan Perubahan',
    'busyLabel' => 'Menyimpan…',
    'useDraft' => false,
    'clearFields' => [],
    'showClose' => false,
    'transition' => true,
    'contentClass' => 'p-4 text-sm',
    'fieldsClass' => 'space-y-3',
    'closeOnBackdrop' => true,
    'scrollContent' => true,
])

{{-- Mount inside Livewire, outside any other form. Supply fields (not a form) in the slot.
    <x-modal-edit name="records" load-action="loadRecord" action="saveRecord">
        ... fields using wire:model and server validation messages using @error ...
    </x-modal-edit>
    Open: $dispatch('modal-edit:open', { name: 'records', id: recordId })
    Actions receive the ID and return { success: true }; see modal-edit.js for the contract.
--}}
<div x-data="modalEdit(@js(['name' => $name, 'action' => $action, 'loadAction' => $loadAction, 'title' => $title, 'description' => $description, 'useDraft' => $useDraft, 'clearFields' => $clearFields]))" x-id="['modal-edit-title', 'modal-edit-description']"
    x-on:modal-edit:open.window="openModal($event.detail)" x-on:keydown.escape.window="closeModal()"
    @if ($closeOnBackdrop) x-on:click.self="closeModal()" @endif x-cloak x-show="isOpen"
    x-trap.inert.noscroll.noreturn="isOpen"
    @if ($transition) x-transition:enter="transition-opacity duration-200 ease-out motion-reduce:transition-none"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity duration-200 ease-in motion-reduce:transition-none"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @endif
    role="dialog" aria-modal="true" tabindex="-1" :aria-labelledby="$id('modal-edit-title')"
    :aria-describedby="description ? $id('modal-edit-description') : null" :aria-busy="busy"
    {{ $attributes->class(['fixed inset-0 z-60 flex items-center justify-center bg-neutral-900/40 p-4 text-neutral-900']) }}>
    <form @submit.prevent="submit()" novalidate x-show="isOpen"
        @if ($transition) x-transition:enter="transition-[opacity,transform] duration-200 ease-out motion-reduce:transition-none"
        x-transition:enter-start="opacity-0 scale-98 motion-reduce:scale-100" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition-[opacity,transform] duration-200 ease-in motion-reduce:transition-none"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-98 motion-reduce:scale-100" @endif
        @class([
            'max-h-[90dvh] w-full max-w-2xl rounded-xl border border-neutral-300 bg-white shadow-lg',
            'flex flex-col overflow-hidden' => $scrollContent,
            'overflow-y-auto' => !$scrollContent,
        ])>
        <div @class([
            'shrink-0 border-b border-neutral-300 px-4 py-3',
            'flex items-center justify-between gap-4' => $showClose,
        ])>
            <div class="space-y-2">
                <h3 :id="$id('modal-edit-title')" class="text-base font-semibold wrap-anywhere" x-text="title"></h3>
                <p x-show="description" :id="$id('modal-edit-description')"
                    class="text-sm text-neutral-700 wrap-anywhere" x-text="description"></p>
            </div>
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
        <div @class([$contentClass, 'min-h-0 overflow-y-auto' => $scrollContent])>
            <p x-cloak x-show="loading" role="status" class="text-neutral-700">Memuat data…</p>
            <p x-cloak x-show="error" x-text="error" role="alert"
                class="mb-3 rounded-lg bg-red/5 p-3 text-sm text-red wrap-anywhere"></p>
            <fieldset x-ref="fields" x-show="ready" :disabled="busy || !ready" class="min-w-0 {{ $fieldsClass }}">
                {{ $slot }}
            </fieldset>
        </div>
        <div class="flex shrink-0 flex-wrap justify-end gap-2 border-t border-neutral-300 px-4 py-3">
            <button x-ref="cancel" type="button" @click="closeModal()" :disabled="busy"
                class="min-h-11 rounded-lg border border-neutral-300 px-3 text-sm font-medium hover:bg-neutral-100 focus-visible:outline-2 focus-visible:outline-primary disabled:opacity-50 sm:min-h-9">{{ $cancelLabel }}</button>
            <button type="submit" :disabled="busy || !ready"
                class="min-h-11 rounded-lg bg-primary px-3 text-sm font-medium text-white hover:bg-primary/90 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:opacity-50 sm:min-h-9">
                <span x-show="!busy || loading">{{ $saveLabel }}</span>
                <span x-cloak x-show="busy && !loading" role="status">{{ $busyLabel }}</span>
            </button>
        </div>
    </form>
</div>
