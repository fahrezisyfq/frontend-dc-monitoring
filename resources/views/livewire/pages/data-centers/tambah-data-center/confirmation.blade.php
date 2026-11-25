<section class="space-y-4 rounded-xl border border-neutral-300 p-4" aria-labelledby="dc-summary-identity">
    <div class="flex items-center justify-between gap-3">
        <h3 id="dc-summary-identity" class="text-base font-semibold">Identitas Pusat Data</h3>
        <x-button variant="secondary" ::disabled="busy" @click="backTo(1)" aria-label="Ubah identitas pusat data" class="px-4! py-2! sm:min-h-11! sm:py-2! transition-none! enabled:active:opacity-100! disabled:cursor-wait! disabled:hover:bg-neutral-100">Ubah</x-button>
    </div>
    <dl class="grid gap-4 md:grid-cols-2">
        @include('livewire.pages.data-centers.tambah-data-center.summary-value', ['summary' => ['label' => 'Perusahaan', 'expression' => "companies[form.company]?.name"]])
        @include('livewire.pages.data-centers.tambah-data-center.summary-value', ['summary' => ['label' => 'NIB', 'expression' => "companies[form.company]?.nib"]])
        @foreach ($identityFields as $key => [$label, $type, $unit])
            @include('livewire.pages.data-centers.tambah-data-center.summary-value', ['summary' => ['label' => $label, 'expression' => 'form.'.$key, 'unit' => $unit]])
        @endforeach
        @foreach (['status' => 'Status Pusat Data', 'province' => 'Provinsi', 'city' => 'Kota/Kabupaten', 'district' => 'Kecamatan', 'ai_ready' => 'AI Ready', 'area' => 'Kawasan'] as $key => $label)
            @include('livewire.pages.data-centers.tambah-data-center.summary-value', ['summary' => ['label' => $label, 'expression' => $key === 'area' ? 'areas[form.area]' : 'form.'.$key]])
        @endforeach
        @include('livewire.pages.data-centers.tambah-data-center.summary-value', ['summary' => ['label' => 'Kode PUM (simulasi)', 'expression' => 'pum']])
        @include('livewire.pages.data-centers.tambah-data-center.summary-value', ['summary' => ['label' => 'Zona Waktu', 'expression' => 'timezone']])
        @include('livewire.pages.data-centers.tambah-data-center.summary-value', ['summary' => ['label' => 'Service Models', 'expression' => "form.services.join(', ')"]])
    </dl>
</section>
<section class="space-y-4 rounded-xl border border-neutral-300 p-4" aria-labelledby="dc-summary-capacity">
    <div class="flex items-center justify-between gap-3">
        <h3 id="dc-summary-capacity" class="text-base font-semibold">Kapasitas &amp; Utilisasi</h3>
        <x-button variant="secondary" ::disabled="busy" @click="backTo(2)" aria-label="Ubah kapasitas dan utilisasi" class="px-4! py-2! sm:min-h-11! sm:py-2! transition-none! enabled:active:opacity-100! disabled:cursor-wait! disabled:hover:bg-neutral-100">Ubah</x-button>
    </div>
    <dl class="grid gap-4 md:grid-cols-2">
        @foreach ($capacityFields as $key => [$label, $type, $unit])
            @include('livewire.pages.data-centers.tambah-data-center.summary-value', ['summary' => ['label' => $label, 'expression' => 'form.'.$key, 'unit' => $unit]])
        @endforeach
        @include('livewire.pages.data-centers.tambah-data-center.summary-value', ['summary' => ['label' => 'Power Usage Effectiveness (PUE)', 'expression' => 'pue']])
        @include('livewire.pages.data-centers.tambah-data-center.summary-value', ['summary' => ['label' => 'Water Usage Effectiveness (WUE)', 'expression' => 'wue', 'unit' => 'm³/kWh']])
    </dl>
</section>
<section class="space-y-4 rounded-xl border border-neutral-300 p-4" aria-labelledby="dc-summary-certificates">
    <div class="flex items-center justify-between gap-3">
        <h3 id="dc-summary-certificates" class="text-base font-semibold">Sertifikasi</h3>
        <x-button variant="secondary" ::disabled="busy" @click="backTo(3)" aria-label="Ubah sertifikasi" class="px-4! py-2! sm:min-h-11! sm:py-2! transition-none! enabled:active:opacity-100! disabled:cursor-wait! disabled:hover:bg-neutral-100">Ubah</x-button>
    </div>
    <template x-for="(certificate, position) in filledCertificates" :key="position">
        <div class="space-y-3 border-t border-neutral-300 pt-4">
            <h4 class="text-sm font-semibold" x-text="'Sertifikasi ' + (position + 1)"></h4>
            <dl class="grid gap-4 md:grid-cols-2">
                @foreach ($certificateFields as $key => [$label])
                    @include('livewire.pages.data-centers.tambah-data-center.summary-value', ['summary' => ['label' => $label, 'expression' => 'certificate.'.$key]])
                @endforeach
            </dl>
        </div>
    </template>
    <div x-show="!filledCertificates.length">
        <p class="text-sm text-neutral-700">Belum ada sertifikasi</p>
    </div>
</section>
