@php
    $model = $field['model'];
    $label = $field['label'];
    $type = $field['type'] ?? 'text';
    $unit = $field['unit'] ?? '';
    $placeholder = $field['placeholder'] ?? '';
    $options = $field['options'] ?? [];
    $required = $field['required'] ?? false;
    $expression = $field['expression'] ?? $model;
    $path = $field['path'] ?? "'" . $model . "'";
    $id = 'dc-' . str_replace('.', '-', $model);
    $classes =
        'min-h-11 w-full rounded-lg border bg-white px-3 py-2 text-sm focus:border-primary focus:outline-2 focus:outline-primary disabled:bg-neutral-100 disabled:text-neutral-700';
@endphp
<x-field :name="$model" :label="$label" :input-id="$id" :required="$required" class="[&>label]:text-sm"
    x-init="connectField($el, {{ $path }})">
    @if ($type === 'select')
        <select :id="fieldId({{ $path }})" x-model="{{ $expression }}" :disabled="busy"
            :aria-invalid="errorFor({{ $path }}) ? 'true' : 'false'"
            :aria-describedby="fieldId({{ $path }}) + '-error'"
            :class="errorFor({{ $path }}) ? 'border-red' : 'border-neutral-300'" class="{{ $classes }}">
            <option value=""
                @isset($field['dynamicPlaceholder']) x-text="{{ $field['dynamicPlaceholder'] }}" @endisset>
                {{ $placeholder ?: 'Pilih ' . $label }}</option>
            @isset($field['dynamicOptions'])
                <template x-for="(text, value) in {{ $field['dynamicOptions'] }}" :key="value">
                    <option :value="value" x-text="text"></option>
                </template>
            @else
                @foreach ($options as $value => $text)
                    <option value="{{ $value }}">{{ $text }}</option>
                @endforeach
            @endisset
        </select>
    @elseif ($type === 'textarea')
        <textarea :id="fieldId({{ $path }})" x-model="{{ $expression }}" :disabled="busy" rows="3"
            maxlength="2000" placeholder="{{ $placeholder }}"
            :aria-invalid="errorFor({{ $path }}) ? 'true' : 'false'"
            :aria-describedby="fieldId({{ $path }}) + '-error'"
            :class="errorFor({{ $path }}) ? 'border-red' : 'border-neutral-300'" class="{{ $classes }}"></textarea>
    @elseif ($type === 'number' && $unit !== '')
        <div class="flex min-h-11 rounded-lg border bg-white focus-within:border-primary focus-within:outline-2 focus-within:outline-primary"
            :class="errorFor({{ $path }}) ? 'border-red' : 'border-neutral-300'">
            <input :id="fieldId({{ $path }})" x-model="{{ $expression }}" :disabled="busy"
                type="number" min="0" step="any" inputmode="decimal" @required($required)
                placeholder="{{ $placeholder }}" :aria-invalid="errorFor({{ $path }}) ? 'true' : 'false'"
                :aria-describedby="fieldId({{ $path }}) + '-unit ' + fieldId({{ $path }}) + '-error'"
                class="min-w-0 flex-1 rounded-l-lg bg-transparent px-3 py-2 text-sm tabular-nums focus:outline-none">
            <span :id="fieldId({{ $path }}) + '-unit'"
                class="flex min-w-14 shrink-0 select-none items-center justify-center rounded-r-lg border-l border-neutral-300 bg-neutral-100 px-3 text-sm text-neutral-700">{{ $unit }}</span>
        </div>
    @else
        <input :id="fieldId({{ $path }})" x-model="{{ $expression }}" :disabled="busy"
            type="{{ $type === 'year' ? 'text' : $type }}"
            @if ($type === 'number') min="0" step="any" inputmode="decimal" @elseif ($type === 'year') inputmode="numeric" maxlength="4" pattern="[1-9][0-9]{3}" @elseif ($type === 'text') maxlength="255" @endif
            @required($required) placeholder="{{ $placeholder }}"
            :aria-invalid="errorFor({{ $path }}) ? 'true' : 'false'"
            :aria-describedby="fieldId({{ $path }}) + '-error'"
            :class="errorFor({{ $path }}) ? 'border-red' : 'border-neutral-300'" class="{{ $classes }}">
    @endif
    <x-slot:errorMessage x-bind:id="fieldId({{ $path }}) + '-error'"
        x-text="errorFor({{ $path }})"></x-slot:errorMessage>
</x-field>
