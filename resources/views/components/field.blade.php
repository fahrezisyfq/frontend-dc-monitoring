@props([
    'label',
    'name',
    'required' => false,
    'hint' => null,
    'error' => null,
    'inputId' => null,
    'hintId' => null,
    'errorId' => null,
])

@php($fieldId = $inputId ?? $name)

{{-- Set the slotted input's id to input-id (or name when omitted).
    Input attributes such as required, aria-invalid, wire:model and x-model belong on the input.
    Use aria-describedby="{inputId}-hint {inputId}-error" when a hint is provided,
    or just "{inputId}-error" otherwise. Use distinct input-id values for repeated fields.
    Additional component attributes are applied to the wrapper.
    An optional errorMessage slot forwards its attributes to the error paragraph,
    allowing Alpine x-text without introducing form state in this component.
--}}
<div {{ $attributes->class(['min-w-0']) }}>
    <label for="{{ $fieldId }}" class="mb-1 block text-xs font-medium">
        {{ $label }}
        @if ($required)
            <span class="text-red" aria-hidden="true">*</span>
            <span class="sr-only">(wajib diisi)</span>
        @endif
    </label>

    {{ $slot }}

    @if (filled($hint))
        <p id="{{ $hintId ?? $fieldId . '-hint' }}" class="mt-1 text-xs text-neutral-700">{{ $hint }}</p>
    @endif

    <p
        {{ ($errorMessage->attributes ?? new \Illuminate\View\ComponentAttributeBag())->class(['mt-1 text-xs text-red'])->merge(['id' => $errorId ?? $fieldId . '-error', 'aria-live' => 'polite', 'aria-atomic' => 'true']) }}>
        {{ $errorMessage ?? ($error ?? (isset($errors) ? $errors->first($name) : '')) }}</p>
</div>
