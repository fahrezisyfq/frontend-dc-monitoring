@props(['variant' => 'neutral'])

@php
    $variantClasses = [
        'primary' => 'bg-primary/10 text-primary',
        'purple' => 'bg-purple-100 text-purple-800',
        'success' => 'bg-primary text-white',
        'info' => 'bg-cyan/15 text-neutral-900',
        'warning' => 'bg-yellow/20 text-neutral-900',
        'danger' => 'bg-red/10 text-red',
        'neutral' => 'bg-neutral-100 text-neutral-700',
    ];
@endphp

<span {{ $attributes->class([
    'inline-flex whitespace-nowrap rounded-full px-2 py-0.5 text-xs font-medium',
    $variantClasses[$variant] ?? $variantClasses['neutral'],
]) }}>{{ $slot }}</span>
