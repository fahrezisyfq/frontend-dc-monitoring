<div class="space-y-4 [&_button]:transition-[background-color,color,opacity] [&_button]:duration-200 [&_button]:ease-out [&_button:not(:disabled):active]:opacity-80 motion-reduce:[&_button]:transition-none"
    x-data="{ modalActive: false }" @drawer:opened="modalActive = true" @drawer:closed="modalActive = false"
    @modal-edit:opened="modalActive = true" @modal-edit:closed="modalActive = false"
    @modal-delete:opened="modalActive = true" @modal-delete:closed="modalActive = false">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold tracking-tight">Daftar Pusat Data</h2>
            <p class="mt-1 text-xs text-neutral-700">Lihat detail dan kelola data pusat data yang terdaftar.</p>
        </div>
    </div>

    <x-toast />

    @include('livewire.pages.data-centers.daftar-data-center.table')

    @include('livewire.pages.data-centers.daftar-data-center.drawer')

    @include('livewire.pages.data-centers.daftar-data-center.modals')
</div>
