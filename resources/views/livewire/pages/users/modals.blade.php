<x-modal-edit name="user-edit" action="saveUser" title="Edit Pengguna" :use-draft="true"
    :clear-fields="['password', 'confirmation']" :show-close="true" :transition="false"
    content-class="px-4 py-3" fields-class="space-y-2.5" busy-label="Menyimpan..." wire:key="user-edit-modal">
    <p class="text-sm text-neutral-700">Kosongkan password baru jika tidak ingin menggantinya.</p>
    <p x-show="Object.keys(errors).length" role="alert" class="rounded-lg bg-red/5 p-3 text-sm text-red">Periksa kembali field yang ditandai di bawah.</p>
    <x-field label="Nama Pengguna" name="form.name" input-id="user-name">
        <input id="user-name" x-model="draft.name" :disabled="busy" type="text"
            required maxlength="255" autocomplete="name"
            :aria-invalid="errorFor('name', false) ? 'true' : 'false'" aria-describedby="user-name-error"
            :class="errorFor('name', false) ? 'border-red' : 'border-neutral-300'"
            class="min-h-10 sm:min-h-9 w-full rounded-lg border px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
        <x-slot:errorMessage x-text="errorFor('name', false)"></x-slot:errorMessage>
    </x-field>

    <x-field label="Email" name="form.email" input-id="user-email">
        <input id="user-email" x-model="draft.email" :disabled="busy" type="email"
            required maxlength="255" autocomplete="email"
            :aria-invalid="errorFor('email', false) ? 'true' : 'false'" aria-describedby="user-email-error"
            :class="errorFor('email', false) ? 'border-red' : 'border-neutral-300'"
            class="min-h-10 sm:min-h-9 w-full rounded-lg border px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
        <x-slot:errorMessage x-text="errorFor('email', false)"></x-slot:errorMessage>
    </x-field>

    <x-field label="Password Baru (opsional)" name="form.password" input-id="user-password">
        <input id="user-password" x-model="draft.password" :disabled="busy" type="password"
            autocomplete="new-password" :required="false || !!draft.password || !!draft.confirmation"
            :aria-invalid="errorFor('password', false) ? 'true' : 'false'" aria-describedby="user-password-error"
            :class="errorFor('password', false) ? 'border-red' : 'border-neutral-300'"
            class="min-h-10 sm:min-h-9 w-full rounded-lg border px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
        <x-slot:errorMessage x-text="errorFor('password', false)"></x-slot:errorMessage>
    </x-field>

    <x-field label="Konfirmasi Password Baru (opsional)" name="form.confirmation" input-id="user-confirmation">
        <input id="user-confirmation" x-model="draft.confirmation" :disabled="busy" type="password"
            autocomplete="new-password" :required="false || !!draft.password || !!draft.confirmation"
            :aria-invalid="errorFor('confirmation', false) ? 'true' : 'false'" aria-describedby="user-confirmation-error"
            :class="errorFor('confirmation', false) ? 'border-red' : 'border-neutral-300'"
            class="min-h-10 sm:min-h-9 w-full rounded-lg border px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
        <x-slot:errorMessage x-text="errorFor('confirmation', false)"></x-slot:errorMessage>
    </x-field>

    <x-field label="Role" name="form.role" input-id="user-role">
        <select id="user-role" x-model="draft.role" :disabled="busy" required
            :aria-invalid="errorFor('role', false) ? 'true' : 'false'" aria-describedby="user-role-error"
            :class="errorFor('role', false) ? 'border-red' : 'border-neutral-300'"
            class="min-h-10 sm:min-h-9 w-full rounded-lg border bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
            <option value="" disabled>Pilih role</option>
            @foreach ($roles as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <x-slot:errorMessage x-text="errorFor('role', false)"></x-slot:errorMessage>
    </x-field>
</x-modal-edit>

<x-modal-delete name="user-delete" action="deleteUser" title="Hapus Pengguna" :show-close="true"
    :transition="false" busy-label="Hapus" wire:key="user-delete-modal">
    <x-slot:confirmation>
        <p>Hapus <strong class="wrap-anywhere" x-text="dataName"></strong> dari daftar pengguna?</p>
    </x-slot:confirmation>
    <p class="text-neutral-700">Aksi ini hanya simulasi. Muat ulang halaman untuk mengembalikan data awal.</p>
</x-modal-delete>
