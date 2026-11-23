@php
    $label = $summary['label'];
    $expression = $summary['expression'];
    $unit = $summary['unit'] ?? '';
@endphp
<div class="min-w-0">
    <dt class="text-xs text-neutral-700">{{ $label }}</dt>
    <dd class="mt-1 whitespace-pre-line text-sm font-medium wrap-anywhere" x-text="display({{ $expression }}, @js($unit))"></dd>
</div>
