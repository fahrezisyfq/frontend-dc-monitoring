@php
    $selection = $mode === 'edit' ? 'draft.provider_ids' : '$wire.form.provider_ids';
    $lock = $mode === 'edit' ? 'busy' : 'providerAdding';
    $prefix = 'area-' . $mode . '-provider';
@endphp
<section class="border-t border-neutral-300 pt-4" x-data="{
    providerOpen: false,
    providerSaving: false,
    providerDraft: { name: '', service: '' },
    providerErrors: {},
    providerMessage: '',
    alive: true,
    resetProvider() {
        this.providerOpen = false;
        this.providerDraft = { name: '', service: '' };
        this.providerErrors = {};
        this.providerMessage = '';
    },
    async saveProvider(lock) {
        if (this.providerSaving) return;
        this.providerSaving = true;
        lock(true);
        this.providerErrors = {};
        this.providerMessage = '';
        try {
            const result = await this.$wire.addProvider({ ...this.providerDraft });
            if (!this.alive) return;
            if (result?.success === true) {
                this.$dispatch('area-provider-added', { id: String(result.id) });
                this.resetProvider();
                this.$nextTick(() => this.$refs.providerToggle.focus());
            } else {
                this.providerErrors = result?.errors ?? {};
                this.providerMessage = result?.message ?? '';
            }
        } catch {
            if (this.alive) this.providerMessage = 'Provider belum berhasil ditambahkan. Silakan coba lagi.';
        } finally {
            this.providerSaving = false;
            lock(false);
        }
    },
    destroy() { this.alive = false; }
}"
    @area-provider-added.stop="{{ $selection }} = [...new Set([ ...({{ $selection }} ?? []), $event.detail.id ])]"
    @if ($mode === 'edit')
    @modal-edit:opened.window="if ($event.detail.name === 'area-edit') resetProvider()"
    @endif>
    <h4 id="{{ $prefix }}-title" class="mb-3 text-sm font-semibold">Informasi Provider</h4>
    <fieldset aria-labelledby="{{ $prefix }}-title" aria-describedby="{{ $prefix }}-error" class="min-w-0">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <template x-for="(provider, id) in $wire.providers" :key="id">
                <label
                    class="flex min-h-11 cursor-pointer items-start gap-3 rounded-lg border px-3 py-2 text-sm transition-colors hover:bg-neutral-100 focus-within:outline-2 focus-within:outline-primary"
                    :class="({{ $selection }} ?? []).includes(String(id)) ? 'border-primary bg-primary/5' :
                        'border-neutral-300 bg-white'">
                    <input type="checkbox" :value="String(id)" x-model="{{ $selection }}"
                        :disabled="providerSaving"
                        class="mt-0.5 size-4 shrink-0 accent-primary focus-visible:outline-2 focus-visible:outline-primary">
                    <span class="min-w-0 wrap-anywhere">
                        <span class="block font-medium" x-text="provider.name"></span>
                        <span class="mt-0.5 block text-xs text-neutral-700" x-text="provider.service"></span>
                    </span>
                </label>
            </template>
        </div>
        <p x-show="!Object.keys($wire.providers).length" class="text-xs text-neutral-700">Belum ada provider tersedia.
        </p>
        @if ($mode === 'edit')
            <p id="{{ $prefix }}-error" class="mt-1 text-xs text-red" aria-live="polite"
                x-text="Object.entries(errors).find(([key]) => key === 'form.provider_ids' || key.startsWith('form.provider_ids.'))?.[1]?.[0] ?? ''">
            </p>
        @else
            <p id="{{ $prefix }}-error" class="mt-1 text-xs text-red" aria-live="polite">
                {{ $errors->first('form.provider_ids') ?: $errors->first('form.provider_ids.*') }}</p>
        @endif
    </fieldset>
    <x-button variant="ghost" class="mt-2" x-ref="providerToggle" ::disabled="providerSaving"
        aria-controls="{{ $prefix }}-panel" ::aria-expanded="providerOpen"
        @click="providerOpen = !providerOpen; if (providerOpen) $nextTick(() => $refs.providerName.focus())">+ Tambah
        Provider</x-button>
    <div id="{{ $prefix }}-panel" x-cloak x-show="providerOpen" x-collapse>
        <div class="mt-3 space-y-3 rounded-lg border border-neutral-300 bg-neutral-100/50 p-3">
            <p x-show="providerMessage" x-text="providerMessage" role="alert" class="text-xs text-red"></p>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                @keydown.enter.prevent.stop="saveProvider(value => {{ $lock }} = value)">
                <x-field label="Nama Provider" name="provider.name" :input-id="$prefix . '-name'" required>
                    <input id="{{ $prefix }}-name" x-ref="providerName" x-model="providerDraft.name"
                        type="text" required maxlength="255" :disabled="providerSaving"
                        :aria-invalid="providerErrors.name ? 'true' : 'false'"
                        aria-describedby="{{ $prefix }}-name-error"
                        class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                    <x-slot:errorMessage x-text="providerErrors.name?.[0] ?? ''"></x-slot:errorMessage>
                </x-field>
                <x-field label="Jenis Layanan" name="provider.service" :input-id="$prefix . '-service'" required>
                    <input id="{{ $prefix }}-service" x-model="providerDraft.service" type="text" required
                        maxlength="255" :disabled="providerSaving"
                        :aria-invalid="providerErrors.service ? 'true' : 'false'"
                        aria-describedby="{{ $prefix }}-service-error"
                        class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                    <x-slot:errorMessage x-text="providerErrors.service?.[0] ?? ''"></x-slot:errorMessage>
                </x-field>
            </div>
            <div class="flex justify-end gap-2">
                <x-button variant="secondary" ::disabled="providerSaving"
                    @click="resetProvider(); $refs.providerToggle.focus()">Batal</x-button>
                <x-button ::disabled="providerSaving" @click="saveProvider(value => {{ $lock }} = value)">
                    <span x-text="providerSaving ? 'Menyimpan...' : 'Simpan'"></span>
                </x-button>
            </div>
        </div>
    </div>
</section>
