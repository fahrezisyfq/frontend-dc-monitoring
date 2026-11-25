@props([
    'name',
    'label',
    'type' => 'text',
    'unit' => '',
    'placeholder' => '',
    'options' => [],
    'required' => false,
    'live' => false,
])

@php
    $id = 'dc-edit-'.str_replace('.', '-', $name);
    $errorKey = json_encode($name, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $inputClasses = 'min-h-11 w-full rounded-lg border bg-white px-3 py-2 text-sm focus:border-primary focus:outline-2 focus:outline-primary';
@endphp

<x-field :name="$name" :label="$label" :input-id="$id" :required="$required" class="[&>label]:text-sm">
    @if ($type === 'select')
        <select id="{{ $id }}" @if ($live) wire:model.live.debounce.500ms="{{ $name }}" @else wire:model="{{ $name }}" @endif aria-describedby="{{ $id }}-error"
            :aria-invalid="errors[@js($name)] ? 'true' : 'false'" class="{{ $inputClasses }}"
            :class="errors[@js($name)] ? 'border-red' : 'border-neutral-300'">
            <option value="">{{ $placeholder ?: 'Pilih '.$label }}</option>
            @foreach ($options as $value => $text)
                <option value="{{ $value }}">{{ $text }}</option>
            @endforeach
        </select>
    @elseif ($type === 'textarea')
        <textarea id="{{ $id }}" @if ($live) wire:model.live.debounce.500ms="{{ $name }}" @else wire:model="{{ $name }}" @endif rows="3" maxlength="2000"
            placeholder="{{ $placeholder }}" aria-describedby="{{ $id }}-error"
            :aria-invalid="errors[@js($name)] ? 'true' : 'false'" class="{{ $inputClasses }}"
            :class="errors[@js($name)] ? 'border-red' : 'border-neutral-300'"></textarea>
    @elseif ($type === 'number' && $unit !== '')
        <div class="flex min-h-11 rounded-lg border focus-within:border-primary focus-within:outline-2 focus-within:outline-primary"
            :class="errors[@js($name)] ? 'border-red' : 'border-neutral-300'">
            <input id="{{ $id }}" @if ($live) wire:model.live.debounce.500ms="{{ $name }}" @else wire:model="{{ $name }}" @endif type="number" min="0" step="any" inputmode="decimal"
                placeholder="{{ $placeholder }}" aria-describedby="{{ $id }}-unit {{ $id }}-error"
                :aria-invalid="errors[@js($name)] ? 'true' : 'false'"
                class="min-w-0 flex-1 rounded-l-lg bg-transparent px-3 py-2 text-sm tabular-nums focus:outline-none">
            <span id="{{ $id }}-unit"
                class="flex min-w-14 shrink-0 select-none items-center justify-center rounded-r-lg border-l border-neutral-300 bg-neutral-100 px-3 text-sm text-neutral-700">{{ $unit }}</span>
        </div>
    @else
        <input id="{{ $id }}" @if ($live) wire:model.live.debounce.500ms="{{ $name }}" @else wire:model="{{ $name }}" @endif
            type="{{ $type === 'year' ? 'text' : ($type === 'date' ? 'date' : 'text') }}"
            @if ($type === 'year') inputmode="numeric" maxlength="4" pattern="[1-9][0-9]{3}" @endif
            @if ($type === 'text') maxlength="255" @endif
            placeholder="{{ $placeholder }}" aria-describedby="{{ $id }}-error"
            :aria-invalid="errors[@js($name)] ? 'true' : 'false'" class="{{ $inputClasses }}"
            :class="errors[@js($name)] ? 'border-red' : 'border-neutral-300'">
    @endif

    <x-slot:errorMessage x-text="(errors[{{ $errorKey }}] || [])[0] || ''"></x-slot:errorMessage>
</x-field>
