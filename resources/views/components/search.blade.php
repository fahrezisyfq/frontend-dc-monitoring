@props([
    'placeholder' => 'Cari data...',
    'id' => null,
    'disabled' => false,
    'withIcon' => true,
])

{{-- Classes and binding attributes are forwarded to the input. --}}
@if ($withIcon)
<div class="relative w-full min-w-0">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"
        class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-neutral-500">
        {{-- Lucide: search --}}
        <circle cx="11" cy="11" r="8" />
        <path d="m21 21-4.3-4.3" />
    </svg>
@endif

    <input {{ $attributes->class([
        'min-h-10 w-full rounded-lg border border-neutral-300 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9',
        'block bg-white pr-3 pl-9 text-neutral-900 placeholder:text-neutral-500 transition-colors duration-200 ease-out disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none' => $withIcon,
        'px-3' => ! $withIcon,
    ])->merge([
        'type' => 'search',
        'id' => $id,
        'placeholder' => $placeholder,
        'disabled' => $disabled,
        'aria-label' => 'Pencarian',
    ]) }}>
@if ($withIcon)
</div>
@endif
