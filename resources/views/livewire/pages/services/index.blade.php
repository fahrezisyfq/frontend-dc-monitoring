<div class="space-y-4" x-data="{ modalActive: false }"
    @modal-edit:opened="modalActive = true" @modal-edit:closed="modalActive = false"
    @modal-delete:opened="modalActive = true"
    @modal-delete:closed="modalActive = false; if ($event.detail.triggerRemoved) $nextTick(() => $refs.serviceName.focus())">
    <div>
        <h2 class="text-xl font-semibold tracking-tight">Service</h2>
        <p class="mt-1 text-xs text-neutral-700">Kelola referensi jenis layanan Data Center.</p>
    </div>

    <x-toast />

    @include('livewire.pages.services.form')
    @include('livewire.pages.services.table')
    @include('livewire.pages.services.modals')
</div>
