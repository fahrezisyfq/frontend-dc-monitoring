@props([
    'name' => 'drawer',
    'title' => 'Detail',
    'description' => '',
    'size' => 'md',
    'closeOnEscape' => true,
    'closeOnOutside' => true,
])

@php
    $sizes = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-2xl',
    ];
@endphp

{{-- Mount outside forms, with a unique name and a stable wire:key when used in a loop. --}}
<div x-data="drawer(@js(['name' => $name, 'closeOnEscape' => $closeOnEscape, 'closeOnOutside' => $closeOnOutside]))" x-id="['drawer-title', 'drawer-description']"
    x-on:drawer:open.window="openDrawer($event.detail)" x-on:drawer:close.window="handleClose($event.detail)"
    x-on:keydown.escape.window="handleEscape($event)" x-on:livewire:navigating.document="closeDrawer()" x-cloak
    x-show="isOpen" {{ $attributes->class(['pointer-events-none fixed inset-0 h-dvh z-55 text-neutral-900']) }}>
    <div x-show="isOpen" x-on:click="handleOutside()" aria-hidden="true"
        x-transition:enter="transition-opacity duration-200 ease-out motion-reduce:transition-none"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-200 ease-in motion-reduce:transition-none"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="pointer-events-auto absolute inset-0 bg-neutral-900/40"></div>

    <section x-ref="panel" x-show="isOpen" x-trap.inert.noscroll="isOpen"
        x-transition:enter="transition-transform duration-200 ease-out motion-reduce:transition-none"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition-transform duration-200 ease-in motion-reduce:transition-none"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" role="dialog"
        aria-modal="true" tabindex="-1" :aria-labelledby="$id('drawer-title')"
        @if ($description !== '') :aria-describedby="$id('drawer-description')" @endif
        @class([
            'pointer-events-auto absolute inset-y-0 right-0 flex w-full flex-col border-l border-neutral-300 bg-white shadow-lg',
            $sizes[$size] ?? $sizes['md'],
        ])>
        <header class="flex shrink-0 items-center justify-between gap-4 border-b border-neutral-300 px-4 py-3">
            <div class="min-w-0">
                <h2 :id="$id('drawer-title')" class="text-base font-semibold wrap-anywhere">{{ $title }}</h2>
                @if ($description !== '')
                    <p :id="$id('drawer-description')" class="mt-1 text-sm text-neutral-700 wrap-anywhere">
                        {{ $description }}</p>
                @endif
            </div>
            <button type="button" x-on:click="closeDrawer()" aria-label="Tutup panel"
                class="flex size-11 shrink-0 items-center justify-center rounded-lg text-neutral-700 hover:bg-neutral-100 focus-visible:outline-2 focus-visible:outline-primary sm:size-9">
                {{-- Lucide: x --}}
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" class="size-4" aria-hidden="true">
                    <path d="m18 6-12 12M6 6l12 12" />
                </svg>
            </button>
        </header>
        <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain p-4 text-sm">{{ $slot }}</div>
        @isset($footer)
            <footer
                {{ $footer->attributes->class(['flex shrink-0 flex-wrap justify-end gap-2 border-t border-neutral-300 px-4 py-3']) }}>
                {{ $footer }}</footer>
        @endisset
    </section>
</div>
