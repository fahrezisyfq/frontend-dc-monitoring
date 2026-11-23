@props([
    'label' => 'Tabel data',
    'minWidth' => 'min-w-240',
    'framed' => true,
])

{{-- The header slot contains <tr>/<th>; the default slot contains <tr>/<td>.
    Render empty-state rows and their colspan in the caller, for example with @forelse.
    Attributes on this component go to <table>; attributes on the header slot go to <thead>.
    An optional body slot forwards its attributes to <tbody> (for example wire:loading).
    Low-specificity :where selectors let cell/row utilities override the default styling.
--}}
<div @class([
    'min-w-0',
    'overflow-hidden rounded-xl border border-neutral-300 bg-white' => $framed,
])>
    <div class="overflow-x-auto" tabindex="0" role="region"
        aria-label="{{ $label }}, gulir horizontal pada layar kecil">
        <table
            {{ $attributes->class(['w-full text-left text-sm text-neutral-900', $minWidth])->merge(['aria-label' => $label]) }}>
            <thead
                {{ ($header->attributes ?? new \Illuminate\View\ComponentAttributeBag())->class(['border-b border-neutral-300 bg-neutral-100 text-neutral-700 [:where(&>tr>th)]:px-3 [:where(&>tr>th)]:py-2 [:where(&>tr>th)]:text-left [:where(&>tr>th)]:font-semibold']) }}>
                {{ $header ?? '' }}
            </thead>
            <tbody
                {{ ($body->attributes ?? new \Illuminate\View\ComponentAttributeBag())->class(['divide-y divide-neutral-300 [:where(&>tr)]:transition-colors [:where(&>tr)]:duration-200 [:where(&>tr)]:ease-out motion-reduce:[:where(&>tr)]:transition-none [:where(&>tr:hover)]:bg-neutral-100/50 [:where(&>tr>td)]:px-3 [:where(&>tr>td)]:py-2.5 [:where(&>tr>td)]:align-middle']) }}>
                {{ $body ?? $slot }}
            </tbody>
        </table>
    </div>
</div>
