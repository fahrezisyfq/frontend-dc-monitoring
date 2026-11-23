{{-- Mount once per page; Alpine owns and cleans up both window listeners. --}}
<div x-data="toastNotifications" x-on:toast.window="enqueue($event.detail)" x-on:livewire:navigating.window="clear()"
    class="pointer-events-none fixed inset-x-4 top-4 z-70 sm:left-auto sm:w-96">
    <div x-cloak x-show="visible"
        x-transition:enter="transition-[opacity,transform] duration-200 ease-out motion-reduce:transition-none"
        x-transition:enter-start="opacity-0 -translate-y-2 motion-reduce:translate-y-0"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition-[opacity,transform] duration-200 ease-in motion-reduce:transition-none"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2 motion-reduce:translate-y-0"
        class="pointer-events-auto flex items-start gap-3 rounded-xl border border-neutral-300 bg-white p-4 shadow-sm"
        :role="current?.type === 'error' ? 'alert' : 'status'"
        :aria-live="current?.type === 'error' ? 'assertive' : 'polite'" aria-atomic="true">
        <svg x-show="current?.type === 'success'" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
            class="mt-0.5 size-5 shrink-0 text-primary" aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
            <path d="m9 12 2 2 4-4" />
        </svg>
        <svg x-show="current?.type === 'error'" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 size-5 shrink-0 text-red"
            aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 8v4M12 16h.01" />
        </svg>
        <p class="min-w-0 flex-1 text-sm leading-6 text-neutral-900 wrap-anywhere" x-text="current?.message ?? ''"></p>
        <button type="button" @click="dismiss()" aria-label="Tutup notifikasi"
            class="-m-2 flex size-11 shrink-0 items-center justify-center rounded-lg text-neutral-700 transition-colors duration-200 hover:bg-neutral-100 focus-visible:outline-2 focus-visible:outline-primary motion-reduce:transition-none">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                class="size-4" aria-hidden="true">
                <path d="m18 6-12 12M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
