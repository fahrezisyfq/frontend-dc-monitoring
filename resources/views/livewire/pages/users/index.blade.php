<div class="space-y-4 [&_button]:transition-colors [&_button]:duration-200 [&_button:disabled]:opacity-50 motion-reduce:[&_button]:transition-none" x-data="{ modalActive: false }"
    @modal-edit:opened="modalActive = true" @modal-edit:closed="modalActive = false"
    @drawer:opened="modalActive = true" @drawer:closed="modalActive = false"
    @modal-delete:opened="modalActive = true"
    @modal-delete:closed="modalActive = false; if ($event.detail.triggerRemoved) $nextTick(() => $refs.createUserName.focus())">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold tracking-tight">Pengguna</h2>
            <p class="mt-1 text-xs text-neutral-700">Kelola daftar pengguna dan hak akses sistem.</p>
        </div>
    </div>

    <x-toast />

    @include('livewire.pages.users.form')

    @include('livewire.pages.users.table')

    @include('livewire.pages.users.modals')

    @include('livewire.pages.users.drawer')
</div>
