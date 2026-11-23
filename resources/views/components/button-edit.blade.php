@props([
    'dataId' => null,
    'dataName' => '',
])

<button
    {{ $attributes->class([
            'inline-flex size-11 shrink-0 items-center justify-center rounded-lg text-primary hover:bg-primary/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:opacity-50 sm:size-9',
        ])->merge([
            'type' => 'button',
            'title' => 'Edit',
            'aria-label' => trim('Edit ' . $dataName),
            'data-id' => $dataId,
            'data-name' => $dataName,
        ]) }}>
    {{-- Lucide: pencil --}}
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
        stroke-linejoin="round" class="size-4" aria-hidden="true" focusable="false">
        <path
            d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497zM15 5l4 4" />
    </svg>
</button>
