@props(['label', 'expression', 'unit' => ''])

<div class="min-w-0">
    <dt class="text-xs text-neutral-700">{{ $label }}</dt>
    <dd class="mt-1 text-sm font-medium tabular-nums wrap-anywhere"
        x-text="dcDetail({{ $expression }}, @js($unit))"></dd>
</div>
