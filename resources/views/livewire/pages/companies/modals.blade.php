<x-modal-edit name="company-edit" action="saveCompany" title="Edit Perusahaan" :use-draft="true" :show-close="true"
    :close-on-backdrop="false" :scroll-content="false" content-class="px-4 py-3" fields-class="space-y-2.5" wire:key="company-edit-modal">
    <p x-cloak x-show="Object.keys(errors).length" role="alert" class="rounded-lg bg-red/5 p-3 text-sm text-red">
        Periksa kembali field yang ditandai di bawah.
    </p>

    <div class="grid grid-cols-1 gap-x-4 gap-y-2.5 lg:grid-cols-2">
        <div class="min-w-0 space-y-2.5">
            <x-field label="Nama Perusahaan" name="form.company_name" input-id="company-company_name"
                error-id="company_name-error">
                <input id="company-company_name" x-model="draft.company_name" :disabled="busy" type="text"
                    required maxlength="255" :aria-invalid="errorFor('company_name') ? 'true' : 'false'"
                    aria-describedby="company_name-error"
                    class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                <x-slot:errorMessage x-text="errorFor('company_name')"></x-slot:errorMessage>
            </x-field>

            <x-field label="NIB" name="form.nib" input-id="company-nib" error-id="nib-error"
                hint="Masukkan 13 digit angka, termasuk angka nol di depan." hint-id="nib-help">
                <input id="company-nib" x-model="draft.nib" :disabled="busy" type="text" required
                    maxlength="13" inputmode="numeric" pattern="[0-9]{13}"
                    :aria-invalid="errorFor('nib') ? 'true' : 'false'" aria-describedby="nib-error nib-help"
                    class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                <x-slot:errorMessage x-text="errorFor('nib')"></x-slot:errorMessage>
            </x-field>
        </div>

        <div class="min-w-0 space-y-2.5">
            <x-field label="Nama Kontak" name="form.contact_name" input-id="company-contact_name"
                error-id="contact_name-error">
                <input id="company-contact_name" x-model="draft.contact_name" :disabled="busy" type="text"
                    required maxlength="255" :aria-invalid="errorFor('contact_name') ? 'true' : 'false'"
                    aria-describedby="contact_name-error"
                    class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                <x-slot:errorMessage x-text="errorFor('contact_name')"></x-slot:errorMessage>
            </x-field>

            <x-field label="Phone" name="form.contact_phone" input-id="company-contact_phone"
                error-id="contact_phone-error">
                <input id="company-contact_phone" x-model="draft.contact_phone" :disabled="busy" type="tel"
                    required maxlength="255" :aria-invalid="errorFor('contact_phone') ? 'true' : 'false'"
                    aria-describedby="contact_phone-error"
                    class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                <x-slot:errorMessage x-text="errorFor('contact_phone')"></x-slot:errorMessage>
            </x-field>

            <x-field label="Email" name="form.contact_email" input-id="company-contact_email"
                error-id="contact_email-error">
                <input id="company-contact_email" x-model="draft.contact_email" :disabled="busy" type="email"
                    required maxlength="255" :aria-invalid="errorFor('contact_email') ? 'true' : 'false'"
                    aria-describedby="contact_email-error"
                    class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                <x-slot:errorMessage x-text="errorFor('contact_email')"></x-slot:errorMessage>
            </x-field>
        </div>

        <div class="min-w-0 space-y-2.5 lg:col-span-2">
            <x-field label="Alamat Perusahaan" name="form.company_address" input-id="company-company_address"
                error-id="company_address-error">
                <textarea id="company-company_address" x-model="draft.company_address" :disabled="busy" rows="2" required
                    maxlength="1000" :aria-invalid="errorFor('company_address') ? 'true' : 'false'"
                    aria-describedby="company_address-error"
                    class="w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary"></textarea>
                <x-slot:errorMessage x-text="errorFor('company_address')"></x-slot:errorMessage>
            </x-field>
        </div>
    </div>
</x-modal-edit>

<x-modal-delete name="company-delete" action="deleteCompany" title="Hapus Perusahaan" :show-close="true"
    :close-on-backdrop="false" wire:key="company-delete-modal">
    <x-slot:confirmation>
        <p>
            Hapus <strong class="wrap-anywhere" x-text="dataName"></strong> dari daftar perusahaan?
        </p>
    </x-slot:confirmation>
    <p class="text-neutral-700">
        Data akan dihapus dari simulasi ini. Muat ulang halaman untuk mengembalikan data awal.
    </p>
</x-modal-delete>
