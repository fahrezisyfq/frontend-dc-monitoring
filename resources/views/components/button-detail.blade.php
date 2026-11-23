@props([
    'dataId' => null,
    'dataName' => '',
])

<button
    {{ $attributes->class([
            'inline-flex size-11 shrink-0 items-center justify-center rounded-lg text-primary hover:bg-primary/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:opacity-50 sm:size-9',
        ])->merge([
            'type' => 'button',
            'title' => 'Detail',
            'aria-label' => trim('Detail ' . $dataName),
            'data-id' => $dataId,
            'data-name' => $dataName,
        ]) }}>
    {{-- Lucide: eye --}}
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
        stroke-linejoin="round" class="size-4" aria-hidden="true" focusable="false">
        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
        <circle cx="12" cy="12" r="3" />
    </svg>
</button>
