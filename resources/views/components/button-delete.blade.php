@props([
    'dataId' => null,
    'dataName' => '',
])

<button
    {{ $attributes->class([
            'inline-flex size-11 shrink-0 items-center justify-center rounded-lg text-red hover:bg-red/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:opacity-50 sm:size-9',
        ])->merge([
            'type' => 'button',
            'title' => 'Hapus',
            'aria-label' => trim('Hapus ' . $dataName),
            'data-id' => $dataId,
            'data-name' => $dataName,
        ]) }}>
    {{-- Lucide: trash-2 --}}
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
        stroke-linejoin="round" class="size-4" aria-hidden="true" focusable="false">
        <path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2M10 11v6M14 11v6" />
    </svg>
</button>
