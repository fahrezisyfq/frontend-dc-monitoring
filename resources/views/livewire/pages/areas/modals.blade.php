<x-modal-edit name="area-edit" title="Edit Kawasan" :use-draft="true" action="saveArea" fields-class="space-y-4"
    wire:key="area-edit-modal">

    <section>
        <h4 class="mb-3 text-sm font-semibold">
            Informasi Kawasan
        </h4>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

            <x-field label="Nama Kawasan" name="form.name" input-id="area-edit-name" required>
                <input id="area-edit-name" x-model="draft.name" aria-describedby="area-edit-name-error"
                    :aria-invalid="errorFor('name') ? 'true' : 'false'" type="text" maxlength="255" required
                    placeholder="Contoh: Kawasan Pesisir Rekaan"
                    class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                <x-slot:errorMessage x-text="errorFor('name')"></x-slot:errorMessage>
            </x-field>

            <x-field label="Kategori Kawasan" name="form.category" input-id="area-edit-category">
                <select id="area-edit-category" x-model="draft.category" aria-describedby="area-edit-category-error"
                    :aria-invalid="errorFor('category') ? 'true' : 'false'"
                    class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                    <option value="KEK">KEK</option>
                    <option value="Kawasan Industri">Kawasan Industri</option>
                    <option value="NON Kawasan">NON Kawasan</option>
                </select>
                <x-slot:errorMessage x-text="errorFor('category')"></x-slot:errorMessage>
            </x-field>

            <x-field label="Total Luas Kawasan" name="form.total_area" input-id="area-edit-total_area">
                <div
                    class="flex min-h-10 min-w-0 overflow-hidden rounded-lg border border-neutral-300 focus-within:border-primary focus-within:outline-2 focus-within:outline-primary sm:min-h-9">
                    <input id="area-edit-total_area" x-model="draft.total_area"
                        aria-describedby="area-edit-total_area-error"
                        :aria-invalid="errorFor('total_area') ? 'true' : 'false'" type="number" min="0"
                        step="any" placeholder="0"
                        class="min-w-0 flex-1 border-0 bg-white px-3 text-sm focus:outline-none disabled:cursor-not-allowed disabled:bg-neutral-100 disabled:text-neutral-700">
                    <span
                        class="pointer-events-none flex shrink-0 select-none items-center border-l border-neutral-300 bg-neutral-100 px-3 text-xs text-neutral-700"
                        aria-hidden="true">ha</span>
                </div>
                <x-slot:errorMessage x-text="errorFor('total_area')"></x-slot:errorMessage>
            </x-field>
        </div>
    </section>

    <section class="border-t border-neutral-300 pt-4">
        <h4 class="mb-3 text-sm font-semibold">Infrastruktur Kawasan</h4>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <x-field label="Desalinasi" name="form.desalination" input-id="area-edit-desalination">
                <select id="area-edit-desalination" x-model="draft.desalination"
                    aria-describedby="area-edit-desalination-error"
                    :aria-invalid="errorFor('desalination') ? 'true' : 'false'"
                    @change="if ($event.target.value !== '1') draft.desalination_flow = ''"
                    class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                    <option value="0">Tidak</option>
                    <option value="1">Ya</option>
                </select>
                <x-slot:errorMessage x-text="errorFor('desalination')"></x-slot:errorMessage>
            </x-field>
            <x-field label="Debit Desalinasi" name="form.desalination_flow" input-id="area-edit-desalination_flow">
                <div
                    class="flex min-h-10 min-w-0 overflow-hidden rounded-lg border border-neutral-300 focus-within:border-primary focus-within:outline-2 focus-within:outline-primary sm:min-h-9">
                    <input id="area-edit-desalination_flow" x-model="draft.desalination_flow"
                        aria-describedby="area-edit-desalination_flow-error"
                        :aria-invalid="errorFor('desalination_flow') ? 'true' : 'false'" type="number" min="0"
                        step="any" :disabled="draft.desalination !== '1' || busy" placeholder="0"
                        class="min-w-0 flex-1 border-0 bg-white px-3 text-sm focus:outline-none disabled:cursor-not-allowed disabled:bg-neutral-100 disabled:text-neutral-700">
                    <span
                        class="pointer-events-none flex shrink-0 select-none items-center border-l border-neutral-300 bg-neutral-100 px-3 text-xs text-neutral-700"
                        aria-hidden="true">l/s</span>
                </div>
                <x-slot:errorMessage x-text="errorFor('desalination_flow')"></x-slot:errorMessage>
            </x-field>
            <x-field label="Water Treatment" name="form.water_treatment" input-id="area-edit-water_treatment">
                <select id="area-edit-water_treatment" x-model="draft.water_treatment"
                    aria-describedby="area-edit-water_treatment-error"
                    :aria-invalid="errorFor('water_treatment') ? 'true' : 'false'"
                    class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                    <option value="0">Tidak</option>
                    <option value="1">Ya</option>
                </select>
                <x-slot:errorMessage x-text="errorFor('water_treatment')"></x-slot:errorMessage>
            </x-field>
            <x-field label="Gardu Induk" name="form.substations" input-id="area-edit-substations">
                <input id="area-edit-substations" x-model="draft.substations"
                    aria-describedby="area-edit-substations-error"
                    :aria-invalid="errorFor('substations') ? 'true' : 'false'" type="number" min="0"
                    step="1" placeholder="0"
                    class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                <x-slot:errorMessage x-text="errorFor('substations')"></x-slot:errorMessage>
            </x-field>
            <x-field label="Electricity Number" name="form.electricity_number" input-id="area-edit-electricity_number">
                <input id="area-edit-electricity_number" x-model="draft.electricity_number"
                    aria-describedby="area-edit-electricity_number-error"
                    :aria-invalid="errorFor('electricity_number') ? 'true' : 'false'" type="number" min="0"
                    step="1" placeholder="0"
                    class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                <x-slot:errorMessage x-text="errorFor('electricity_number')"></x-slot:errorMessage>
            </x-field>
            <x-field label="Cable Landing Station" name="form.cable_landing_station"
                input-id="area-edit-cable_landing_station">
                <select id="area-edit-cable_landing_station" x-model="draft.cable_landing_station"
                    aria-describedby="area-edit-cable_landing_station-error"
                    :aria-invalid="errorFor('cable_landing_station') ? 'true' : 'false'"
                    class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                    <option value="">Pilih Cable Landing Station</option>
                    <option value="Domestic">Domestic</option>
                    <option value="Internasional">Internasional</option>
                </select>
                <x-slot:errorMessage x-text="errorFor('cable_landing_station')"></x-slot:errorMessage>
            </x-field>
        </div>
    </section>
    @include('livewire.pages.areas.providers', ['mode' => 'edit'])
    <section class="border-t border-neutral-300 pt-4">
        <h4 class="mb-3 text-sm font-semibold">Informasi Kontak</h4>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <x-field label="Nama Kontak" name="form.contact_name" input-id="area-edit-contact_name">
                <input id="area-edit-contact_name" x-model="draft.contact_name"
                    aria-describedby="area-edit-contact_name-error"
                    :aria-invalid="errorFor('contact_name') ? 'true' : 'false'" type="text" maxlength="255"
                    placeholder="Nama Kontak (opsional)"
                    class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                <x-slot:errorMessage x-text="errorFor('contact_name')"></x-slot:errorMessage>
            </x-field>
            <x-field label="Email" name="form.email" input-id="area-edit-email">
                <input id="area-edit-email" x-model="draft.email" aria-describedby="area-edit-email-error"
                    :aria-invalid="errorFor('email') ? 'true' : 'false'" type="email" maxlength="255"
                    placeholder="Email (opsional)"
                    class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                <x-slot:errorMessage x-text="errorFor('email')"></x-slot:errorMessage>
            </x-field>
            <x-field label="Phone" name="form.phone" input-id="area-edit-phone">
                <input id="area-edit-phone" x-model="draft.phone" aria-describedby="area-edit-phone-error"
                    :aria-invalid="errorFor('phone') ? 'true' : 'false'" type="tel" maxlength="50"
                    placeholder="Phone (opsional)"
                    class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                <x-slot:errorMessage x-text="errorFor('phone')"></x-slot:errorMessage>
            </x-field>
        </div>
    </section>
</x-modal-edit>

<x-modal-delete name="area-delete" title="Hapus Kawasan" action="deleteArea"
    message="Apakah Anda yakin ingin menghapus kawasan ini?" wire:key="area-delete-modal">
    <p class="text-neutral-700">Kawasan akan dihapus dari daftar simulasi. Muat ulang halaman untuk mengembalikan data
        awal.</p>
</x-modal-delete>
