<section class="min-w-0 rounded-xl border border-neutral-300 bg-white" aria-labelledby="company-create-title">

    <h3 id="company-create-title" class="border-b border-neutral-300 px-4 py-3 text-base font-semibold">
        Tambah Perusahaan
    </h3>

    <form wire:submit="createCompany" novalidate>
        @if ($errors->any() || $createError !== '')
            <p role="alert" class="mt-3 rounded-lg bg-red/5 p-3 text-sm text-red">
                {{ $createError !== '' ? $createError : 'Periksa kembali field yang ditandai di bawah.' }}
            </p>
        @endif

        <fieldset wire:loading.attr="disabled" :disabled="modalActive" class="min-w-0 space-y-2.5 px-4 py-3">
            <div class="grid grid-cols-1 gap-x-4 gap-y-2.5 lg:grid-cols-2">
                <div class="min-w-0 space-y-2.5">

                    <x-field label="Nama Perusahaan" name="form.company_name" input-id="create-company-company_name"
                        error-id="create-company_name-error">
                        <input x-ref="createCompanyName" id="create-company-company_name" wire:model="form.company_name"
                            type="text" required maxlength="255"
                            aria-invalid="{{ $errors->has('form.company_name') ? 'true' : 'false' }}"
                            aria-describedby="create-company_name-error"
                            class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                    </x-field>

                    <x-field label="NIB" name="form.nib" input-id="create-company-nib" error-id="create-nib-error"
                        hint="Masukkan 13 digit angka, termasuk angka nol di depan." hint-id="create-nib-help">
                        <input id="create-company-nib" wire:model="form.nib" type="text" required maxlength="13"
                            inputmode="numeric" pattern="[0-9]{13}"
                            aria-invalid="{{ $errors->has('form.nib') ? 'true' : 'false' }}"
                            aria-describedby="create-nib-error create-nib-help"
                            class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                    </x-field>
                </div>

                <div class="min-w-0 space-y-2.5">
                    <x-field label="Nama Kontak" name="form.contact_name" input-id="create-company-contact_name"
                        error-id="create-contact_name-error">
                        <input id="create-company-contact_name" wire:model="form.contact_name" type="text" required
                            maxlength="255" aria-invalid="{{ $errors->has('form.contact_name') ? 'true' : 'false' }}"
                            aria-describedby="create-contact_name-error"
                            class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                    </x-field>

                    <x-field label="Phone" name="form.contact_phone" input-id="create-company-contact_phone"
                        error-id="create-contact_phone-error">
                        <input id="create-company-contact_phone" wire:model="form.contact_phone" type="tel" required
                            maxlength="255" aria-invalid="{{ $errors->has('form.contact_phone') ? 'true' : 'false' }}"
                            aria-describedby="create-contact_phone-error"
                            class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                    </x-field>

                    <x-field label="Email" name="form.contact_email" input-id="create-company-contact_email"
                        error-id="create-contact_email-error">
                        <input id="create-company-contact_email" wire:model="form.contact_email" type="email" required
                            maxlength="255" aria-invalid="{{ $errors->has('form.contact_email') ? 'true' : 'false' }}"
                            aria-describedby="create-contact_email-error"
                            class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                    </x-field>
                </div>

                <div class="min-w-0 space-y-2.5 lg:col-span-2">
                    <x-field label="Alamat Perusahaan" name="form.company_address"
                        input-id="create-company-company_address" error-id="create-company_address-error">
                        <textarea id="create-company-company_address" wire:model="form.company_address" rows="2" required maxlength="1000"
                            aria-invalid="{{ $errors->has('form.company_address') ? 'true' : 'false' }}"
                            aria-describedby="create-company_address-error"
                            class="w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                        </textarea>
                    </x-field>
                </div>
            </div>
        </fieldset>

        <div class="flex flex-wrap justify-end gap-2 border-t border-neutral-300 px-4 py-3 sm:px-4">
            <x-button variant="secondary" wire:click="resetForm" wire:loading.attr="disabled" ::disabled="modalActive"
                class="py-0! focus-visible:outline-offset-0! disabled:cursor-default! disabled:hover:bg-neutral-100">
                Reset
            </x-button>
            <x-button type="submit" wire:loading.attr="disabled" ::disabled="modalActive"
                class="py-0! disabled:cursor-default! disabled:hover:bg-primary/90">
                <span wire:loading.remove wire:target="createCompany">Simpan Perusahaan</span>
                <span wire:loading wire:target="createCompany" role="status">Menyimpan&hellip;</span>
            </x-button>
        </div>
    </form>
</section>
