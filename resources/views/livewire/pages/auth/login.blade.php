<div class="space-y-6 rounded-xl border border-neutral-300 bg-white p-6 shadow-sm sm:p-8" x-data="{
    form: { email: '', password: '', remember: false },
    errors: {},
    busy: false,
    validate() {
        this.errors = {};

        if (!this.form.email.trim()) {
            this.errors.email = ['Email wajib diisi.'];
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)) {
            this.errors.email = ['Format email tidak valid.'];
        }

        if (!this.form.password) {
            this.errors.password = ['Kata sandi wajib diisi.'];
        }

        return Object.keys(this.errors).length === 0;
    },
    submit() {
        if (!this.validate()) return;

        this.busy = true;
        setTimeout(() => {
            this.busy = false;
            this.$dispatch('toast', {
                type: 'success',
                message: 'Simulasi login berhasil. Tidak ada autentikasi yang dijalankan.'
            });
        }, 400);
    },
    forgotPassword() {
        this.$dispatch('toast', {
            type: 'success',
            message: 'Pemulihan kata sandi belum tersedia pada simulasi ini.'
        });
    }
}">
    <x-toast />

    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-2xl font-semibold tracking-tight">Masuk ke DC Monitoring</h1>
            <p class="mt-2 text-sm text-neutral-700">Halaman simulasi untuk menampilkan alur masuk ke sistem.</p>
        </div>
        <x-badge variant="info">Demo</x-badge>
    </div>

    <form @submit.prevent="submit" novalidate class="space-y-5">
        <x-field label="Email" name="email" input-id="login-email" required>
            <input id="login-email" type="email" x-model="form.email" autocomplete="email"
                placeholder="nama@perusahaan.id" aria-describedby="login-email-error"
                :aria-invalid="errors.email ? 'true' : 'false'"
                :class="errors.email ? 'border-red' : 'border-neutral-300'"
                class="min-h-11 w-full rounded-lg border bg-white px-3 py-2 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
            <x-slot:errorMessage x-text="errors.email?.[0] || ''"></x-slot:errorMessage>
        </x-field>

        <x-field label="Kata sandi" name="password" input-id="login-password" required>
            <input id="login-password" type="password" x-model="form.password" autocomplete="current-password"
                placeholder="Masukkan kata sandi" aria-describedby="login-password-error"
                :aria-invalid="errors.password ? 'true' : 'false'"
                :class="errors.password ? 'border-red' : 'border-neutral-300'"
                class="min-h-11 w-full rounded-lg border bg-white px-3 py-2 text-sm focus:border-primary focus:outline-2 focus:outline-primary">
            <x-slot:errorMessage x-text="errors.password?.[0] || ''"></x-slot:errorMessage>
        </x-field>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <label for="login-remember" class="flex min-h-11 cursor-pointer items-center gap-2 text-sm">
                <input id="login-remember" type="checkbox" x-model="form.remember"
                    class="size-4 accent-primary focus-visible:outline-2 focus-visible:outline-primary">
                Ingat saya
            </label>
            <button type="button" @click="forgotPassword()"
                class="rounded-lg text-sm font-medium text-primary hover:bg-primary/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                Lupa kata sandi?
            </button>
        </div>

        <x-button type="submit" variant="primary" class="w-full justify-center" ::disabled="busy"
            x-bind:aria-busy="busy ? 'true' : 'false'">
            <span x-text="busy ? 'Memproses…' : 'Masuk'">Masuk</span>
        </x-button>
    </form>

    <p class="border-t border-neutral-300 pt-4 text-xs text-neutral-700">
        Halaman ini hanya simulasi frontend. Tidak ada kredensial yang dikirim atau disimpan.
    </p>
</div>
