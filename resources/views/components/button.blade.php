@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false,
    'loadingLabel' => 'Memproses…',
])

@php
    $variants = [
        'primary' => 'bg-primary text-white enabled:hover:bg-primary/90 focus-visible:outline-primary',
        'secondary' =>
            'border border-neutral-300 bg-white text-neutral-900 enabled:hover:bg-neutral-100 focus-visible:outline-primary',
        'danger' => 'bg-red text-white enabled:hover:bg-red/90 focus-visible:outline-red',
        'ghost' => 'bg-transparent text-primary enabled:hover:bg-primary/5 focus-visible:outline-primary',
    ];
    $sizes = [
        'sm' => 'min-h-9 px-3 py-1.5 text-xs',
        'md' => 'min-h-11 px-3 py-2 text-sm sm:min-h-9 sm:py-1.5',
        'lg' => 'min-h-12 px-4 py-3 text-base',
    ];
@endphp

{{-- Pass a boolean loading prop; the caller owns its state and Livewire/Alpine events.
    Optional icon slot: <x-slot:icon>...</x-slot:icon>.
    Supply aria-label when the button contains only an icon.
--}}
<button
    {{ $attributes->class([
            'relative inline-flex shrink-0 items-center justify-center rounded-lg font-medium transition-colors duration-200 ease-out enabled:active:opacity-80 focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none',
            $variants[$variant] ?? $variants['primary'],
            $sizes[$size] ?? $sizes['md'],
        ])->merge(['type' => $type, 'disabled' => $disabled || $loading, 'aria-busy' => $loading ? 'true' : 'false']) }}>
    <span @class([
        'inline-flex min-w-0 items-center justify-center gap-2',
        'opacity-0' => $loading,
    ])>
        @isset($icon)
            <span
                class="inline-flex size-4 shrink-0 items-center justify-center [&>svg]:size-full">{{ $icon }}</span>
        @endisset
        {{ $slot }}
    </span>

    @if ($loading)
        <span class="pointer-events-none absolute inset-0 flex items-center justify-center" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" class="size-4 animate-spin motion-reduce:animate-none"
                focusable="false">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" class="opacity-25" />
                <path d="M12 3a9 9 0 0 1 9 9" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
            </svg>
        </span>
        <span class="sr-only" role="status">{{ $loadingLabel }}</span>
    @endif
</button>
