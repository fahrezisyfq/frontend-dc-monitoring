<section class="rounded-xl border border-neutral-300 bg-white" aria-labelledby="area-create-title">
    <div class="border-b border-neutral-300 px-4 py-3">
        <h3 id="area-create-title" class="text-base font-semibold">Tambah Kawasan</h3>
        <p class="mt-1 text-xs text-neutral-700">Data fiktif untuk simulasi, bukan informasi faktual kawasan. Muat ulang
            untuk mengembalikan data awal.</p>
    </div>
    <form wire:submit="createArea" novalidate>
        <fieldset wire:loading.attr="disabled" :disabled="modalActive || providerAdding" class="min-w-0 space-y-4 p-4">
            <section>
                <h4 class="mb-3 text-sm font-semibold">Informasi Kawasan</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <x-field label="Nama Kawasan" name="form.name" input-id="area-create-name" required>
                        <input id="area-create-name" wire:model="form.name" aria-describedby="area-create-name-error"
                            aria-invalid="{{ $errors->has('form.name') ? 'true' : 'false' }}" type="text"
                            maxlength="255" required x-ref="areaName" placeholder="Contoh: Kawasan Pesisir Rekaan"
                            class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                    </x-field>
                    <x-field label="Kategori Kawasan" name="form.category" input-id="area-create-category">
                        <select id="area-create-category" wire:model="form.category"
                            aria-describedby="area-create-category-error"
                            aria-invalid="{{ $errors->has('form.category') ? 'true' : 'false' }}"
                            class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                            <option value="KEK">KEK</option>
                            <option value="Kawasan Industri">Kawasan Industri</option>
                            <option value="NON Kawasan">NON Kawasan</option>
                        </select>
                    </x-field>
                    <x-field label="Total Luas Kawasan" name="form.total_area" input-id="area-create-total_area">
                        <div
                            class="flex min-h-10 min-w-0 overflow-hidden rounded-lg border border-neutral-300 focus-within:border-primary focus-within:outline-2 focus-within:outline-primary sm:min-h-9">
                            <input id="area-create-total_area" wire:model="form.total_area"
                                aria-describedby="area-create-total_area-error"
                                aria-invalid="{{ $errors->has('form.total_area') ? 'true' : 'false' }}" type="number"
                                min="0" step="any" placeholder="0"
                                class="min-w-0 flex-1 border-0 bg-white px-3 text-sm focus:outline-none disabled:cursor-not-allowed disabled:bg-neutral-100 disabled:text-neutral-700">
                            <span
                                class="pointer-events-none flex shrink-0 select-none items-center border-l border-neutral-300 bg-neutral-100 px-3 text-xs text-neutral-700"
                                aria-hidden="true">ha</span>
                        </div>
                    </x-field>
                </div>
            </section>
            <section class="border-t border-neutral-300 pt-4">
                <h4 class="mb-3 text-sm font-semibold">Infrastruktur Kawasan</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <x-field label="Desalinasi" name="form.desalination" input-id="area-create-desalination">
                        <select id="area-create-desalination" x-model="$wire.form.desalination"
                            aria-describedby="area-create-desalination-error"
                            aria-invalid="{{ $errors->has('form.desalination') ? 'true' : 'false' }}"
                            @change="if ($event.target.value !== '1') $wire.form.desalination_flow = ''"
                            class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </x-field>
                    <x-field label="Debit Desalinasi" name="form.desalination_flow"
                        input-id="area-create-desalination_flow">
                        <div
                            class="flex min-h-10 min-w-0 overflow-hidden rounded-lg border border-neutral-300 focus-within:border-primary focus-within:outline-2 focus-within:outline-primary sm:min-h-9">
                            <input id="area-create-desalination_flow" wire:model="form.desalination_flow"
                                aria-describedby="area-create-desalination_flow-error"
                                aria-invalid="{{ $errors->has('form.desalination_flow') ? 'true' : 'false' }}"
                                type="number" min="0" step="any"
                                :disabled="$wire.form.desalination !== '1'" placeholder="0"
                                class="min-w-0 flex-1 border-0 bg-white px-3 text-sm focus:outline-none disabled:cursor-not-allowed disabled:bg-neutral-100 disabled:text-neutral-700">
                            <span
                                class="pointer-events-none flex shrink-0 select-none items-center border-l border-neutral-300 bg-neutral-100 px-3 text-xs text-neutral-700"
                                aria-hidden="true">l/s</span>
                        </div>
                    </x-field>
                    <x-field label="Water Treatment" name="form.water_treatment" input-id="area-create-water_treatment">
                        <select id="area-create-water_treatment" wire:model="form.water_treatment"
                            aria-describedby="area-create-water_treatment-error"
                            aria-invalid="{{ $errors->has('form.water_treatment') ? 'true' : 'false' }}"
                            class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </x-field>
                    <x-field label="Gardu Induk" name="form.substations" input-id="area-create-substations">
                        <input id="area-create-substations" wire:model="form.substations"
                            aria-describedby="area-create-substations-error"
                            aria-invalid="{{ $errors->has('form.substations') ? 'true' : 'false' }}" type="number"
                            min="0" step="1" placeholder="0"
                            class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                    </x-field>
                    <x-field label="Electricity Number" name="form.electricity_number"
                        input-id="area-create-electricity_number">
                        <input id="area-create-electricity_number" wire:model="form.electricity_number"
                            aria-describedby="area-create-electricity_number-error"
                            aria-invalid="{{ $errors->has('form.electricity_number') ? 'true' : 'false' }}"
                            type="number" min="0" step="1" placeholder="0"
                            class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                    </x-field>
                    <x-field label="Cable Landing Station" name="form.cable_landing_station"
                        input-id="area-create-cable_landing_station">
                        <select id="area-create-cable_landing_station" wire:model="form.cable_landing_station"
                            aria-describedby="area-create-cable_landing_station-error"
                            aria-invalid="{{ $errors->has('form.cable_landing_station') ? 'true' : 'false' }}"
                            class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                            <option value="">Pilih Cable Landing Station</option>
                            <option value="Domestic">Domestic</option>
                            <option value="Internasional">Internasional</option>
                        </select>
                    </x-field>
                </div>
            </section>
            @include('livewire.pages.areas.providers', ['mode' => 'create'])
            <section class="border-t border-neutral-300 pt-4">
                <h4 class="mb-3 text-sm font-semibold">Informasi Kontak</h4>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <x-field label="Nama Kontak" name="form.contact_name" input-id="area-create-contact_name">
                        <input id="area-create-contact_name" wire:model="form.contact_name"
                            aria-describedby="area-create-contact_name-error"
                            aria-invalid="{{ $errors->has('form.contact_name') ? 'true' : 'false' }}" type="text"
                            maxlength="255" placeholder="Nama Kontak (opsional)"
                            class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                    </x-field>
                    <x-field label="Email" name="form.email" input-id="area-create-email">
                        <input id="area-create-email" wire:model="form.email"
                            aria-describedby="area-create-email-error"
                            aria-invalid="{{ $errors->has('form.email') ? 'true' : 'false' }}" type="email"
                            maxlength="255" placeholder="Email (opsional)"
                            class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                    </x-field>
                    <x-field label="Phone" name="form.phone" input-id="area-create-phone">
                        <input id="area-create-phone" wire:model="form.phone"
                            aria-describedby="area-create-phone-error"
                            aria-invalid="{{ $errors->has('form.phone') ? 'true' : 'false' }}" type="tel"
                            maxlength="50" placeholder="Phone (opsional)"
                            class="min-h-10 w-full rounded-lg border border-neutral-300 bg-white px-3 text-sm focus:border-primary focus:outline-2 focus:outline-primary sm:min-h-9">
                    </x-field>
                </div>
            </section>
        </fieldset>
        <div class="flex justify-end gap-2 border-t border-neutral-300 px-4 py-3">
            <x-button variant="secondary" wire:click="resetForm" wire:loading.attr="disabled"
                ::disabled="modalActive || providerAdding">Reset</x-button>
            <x-button type="submit" wire:loading.attr="disabled" ::disabled="modalActive || providerAdding">
                <span wire:loading.remove wire:target="createArea">Simpan</span>
                <span wire:loading wire:target="createArea" role="status">Menyimpan...</span>
            </x-button>
        </div>
    </form>
</section>
