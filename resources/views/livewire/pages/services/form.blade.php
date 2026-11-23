<section class="rounded-xl border border-neutral-300 bg-white" aria-labelledby="service-create-title">
    <div class="border-b border-neutral-300 px-4 py-3">
        <h3 id="service-create-title" class="text-base font-semibold">Tambah Service</h3>
        <p class="mt-1 text-xs text-neutral-700">Data contoh untuk simulasi. Muat ulang halaman untuk mengembalikan data
            awal.</p>
    </div>
    <form wire:submit="createService" novalidate>
        <fieldset wire:loading.attr="disabled" :disabled="modalActive"
            class="grid min-w-0 grid-cols-1 gap-3 p-4 sm:grid-cols-2">
            <x-field label="Nama Service" name="form.name" input-id="service-create-name" required>
                <input id="service-create-name" x-ref="serviceName" type="text" wire:model="form.name" required
                    maxlength="255" placeholder="Contoh: Rack Colocation" aria-describedby="service-create-name-error"
                    aria-invalid="{{ $errors->has('form.name') ? 'true' : 'false' }}"
                    class="min-h-10 w-full rounded-lg border border-neutral-300 px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
            </x-field>
            <x-field label="Description" name="form.description" input-id="service-create-description">
                <textarea id="service-create-description" wire:model="form.description" rows="3" maxlength="2000"
                    placeholder="Deskripsi singkat layanan (opsional)" aria-describedby="service-create-description-error"
                    aria-invalid="{{ $errors->has('form.description') ? 'true' : 'false' }}"
                    class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-primary focus:outline-2 focus:outline-primary"></textarea>
            </x-field>
        </fieldset>
        <div class="flex justify-end gap-2 border-t border-neutral-300 px-4 py-3">
            <x-button variant="secondary" wire:click="resetForm" wire:loading.attr="disabled"
                ::disabled="modalActive">Reset</x-button>
            <x-button type="submit" wire:loading.attr="disabled" ::disabled="modalActive">
                <span wire:loading.remove wire:target="createService">Simpan</span>
                <span wire:loading wire:target="createService" role="status">Menyimpan...</span>
            </x-button>
        </div>
    </form>
</section>
