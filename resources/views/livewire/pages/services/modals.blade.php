<x-modal-edit name="service-edit" title="Edit Service" :use-draft="true" action="saveService" wire:key="service-edit-modal">
    <x-field label="Nama Service" name="form.name" input-id="service-edit-name" required>
        <input id="service-edit-name" type="text" x-model="draft.name" required maxlength="255"
            placeholder="Contoh: Rack Colocation" aria-describedby="service-edit-name-error"
            :aria-invalid="errorFor('name') ? 'true' : 'false'"
            class="min-h-10 w-full rounded-lg border border-neutral-300 px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
        <x-slot:errorMessage x-text="errorFor('name')"></x-slot:errorMessage>
    </x-field>
    <x-field label="Description" name="form.description" input-id="service-edit-description">
        <textarea id="service-edit-description" x-model="draft.description" rows="3" maxlength="2000"
            placeholder="Deskripsi singkat layanan (opsional)" aria-describedby="service-edit-description-error"
            :aria-invalid="errorFor('description') ? 'true' : 'false'"
            class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-primary focus:outline-2 focus:outline-primary"></textarea>
        <x-slot:errorMessage x-text="errorFor('description')"></x-slot:errorMessage>
    </x-field>
</x-modal-edit>

<x-modal-delete name="service-delete" title="Hapus Service" action="deleteService"
    message="Apakah Anda yakin ingin menghapus Service ini?" wire:key="service-delete-modal">
    <p class="text-neutral-700">Service akan dihapus dari daftar simulasi. Muat ulang halaman untuk mengembalikan data awal.</p>
</x-modal-delete>
