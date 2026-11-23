<div class="space-y-4" x-data="{ modalActive: false, providerAdding: false }" @modal-edit:opened="modalActive = true"
    @modal-edit:closed="modalActive = false" @drawer:opened="modalActive = true" @drawer:closed="modalActive = false"
    @modal-delete:opened="modalActive = true"
    @modal-delete:closed="modalActive = false; if ($event.detail.triggerRemoved) $nextTick(() => $refs.areaName.focus())">

    <div>
        <h2 class="text-xl font-semibold tracking-tight">
            Kawasan
        </h2>
        <p class="mt-1 text-xs text-neutral-700">
            Kelola informasi kawasan dan infrastruktur pendukung Data Center.
        </p>

    </div>

    <x-toast />

    @include('livewire.pages.areas.form')
    @include('livewire.pages.areas.table')
    @include('livewire.pages.areas.modals')
    @include('livewire.pages.areas.drawer')

</div>
