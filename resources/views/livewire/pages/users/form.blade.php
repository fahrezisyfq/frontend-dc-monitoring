<section id="user-create-card" wire:key="user-create-card" class="min-w-0 rounded-xl border border-neutral-300 bg-white" aria-labelledby="user-create-title">
    <h3 id="user-create-title" class="border-b border-neutral-300 px-4 py-3 text-base font-semibold">Tambah Pengguna</h3>
    <form wire:submit="createUser" novalidate wire:key="user-create-form">
        <fieldset wire:loading.attr="disabled" :disabled="modalActive" class="min-w-0 space-y-2.5 px-4 py-3">
            <p class="text-sm text-neutral-700">Semua field wajib diisi.</p>
            <div class="grid grid-cols-1 gap-x-4 gap-y-2.5 lg:grid-cols-2">
                <x-field label="Nama Pengguna" name="form.name" input-id="create-user-name">
                    <input x-ref="createUserName" id="create-user-name" wire:model="form.name" type="text"
                        required maxlength="255" autocomplete="name"
                        aria-invalid="{{ $errors->has('form.name') ? 'true' : 'false' }}"
                        aria-describedby="create-user-name-error"
                        class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                </x-field>

                <x-field label="Email" name="form.email" input-id="create-user-email">
                    <input id="create-user-email" wire:model="form.email" type="email"
                        required maxlength="255" autocomplete="email"
                        aria-invalid="{{ $errors->has('form.email') ? 'true' : 'false' }}"
                        aria-describedby="create-user-email-error"
                        class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                </x-field>

                <x-field label="Password" name="form.password" input-id="create-user-password">
                    <input id="create-user-password" wire:model="form.password" type="password"
                        autocomplete="new-password" required
                        aria-invalid="{{ $errors->has('form.password') ? 'true' : 'false' }}"
                        aria-describedby="create-user-password-error"
                        class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                </x-field>

                <x-field label="Konfirmasi Password" name="form.confirmation" input-id="create-user-confirmation">
                    <input id="create-user-confirmation" wire:model="form.confirmation" type="password"
                        autocomplete="new-password" required
                        aria-invalid="{{ $errors->has('form.confirmation') ? 'true' : 'false' }}"
                        aria-describedby="create-user-confirmation-error"
                        class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                </x-field>

                <x-field label="Role" name="form.role" input-id="create-user-role">
                    <select id="create-user-role" wire:model="form.role" required
                        aria-invalid="{{ $errors->has('form.role') ? 'true' : 'false' }}"
                        aria-describedby="create-user-role-error"
                        class="min-h-10 sm:min-h-9 w-full rounded-lg border border-neutral-300 bg-white px-3 py-1.5 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
                        <option value="" disabled>Pilih role</option>
                        @foreach ($roles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </x-field>
            </div>
        </fieldset>
        <div class="flex flex-wrap justify-end gap-2 border-t border-neutral-300 px-4 py-3 sm:px-4">
            <x-button variant="secondary" wire:click="resetForm" wire:loading.attr="disabled" ::disabled="modalActive"
                class="py-0! focus-visible:outline-offset-0! disabled:cursor-default! disabled:hover:bg-neutral-100 enabled:active:opacity-100! ease-in-out!">Reset</x-button>
            <x-button type="submit" variant="primary" wire:loading.attr="disabled" ::disabled="modalActive"
                class="py-0! disabled:cursor-default! disabled:hover:bg-primary/90 enabled:active:opacity-100! ease-in-out!">
                <span wire:loading.remove wire:target="createUser">Simpan Pengguna</span>
                <span wire:loading wire:target="createUser" role="status">Menyimpan&hellip;</span>
            </x-button>
        </div>
    </form>
</section>
