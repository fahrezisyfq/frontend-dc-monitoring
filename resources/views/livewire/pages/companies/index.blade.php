<div class="space-y-4 [&_button]:transition-[background-color,color,opacity] [&_button]:duration-200 [&_button]:ease-out [&_button:not(:disabled):active]:opacity-80 motion-reduce:[&_button]:transition-none"
    x-data="{ modalActive: false }" @modal-edit:opened="modalActive = true" @modal-edit:closed="modalActive = false"
    @modal-delete:opened="modalActive = true"
    @drawer:opened="modalActive = true" @drawer:closed="modalActive = false"
    @modal-delete:closed="modalActive = false; if ($event.detail.triggerRemoved) $nextTick(() => $refs.createCompanyName.focus())">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold tracking-tight">Perusahaan</h2>
            <p class="mt-1 text-xs text-neutral-700">Kelola informasi perusahaan penyedia pusat data.</p>
        </div>
    </div>

    <x-toast />

    @include('livewire.pages.companies.form')

    @include('livewire.pages.companies.table')

    @include('livewire.pages.companies.modals')

    @include('livewire.pages.companies.drawer')

</div>
