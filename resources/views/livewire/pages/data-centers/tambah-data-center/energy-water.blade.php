<div class="flex flex-wrap items-center justify-between gap-3 rounded-lg bg-neutral-100 p-3">
    <div class="text-sm">
        <p class="font-medium">Periode pelaporan tahunan: <span x-text="display(form.reporting_year)"></span></p>
        <p class="mt-1 text-neutral-700"
            x-text="form.reporting_year ? 'Seluruh konsumsi energi dan air di bawah mengacu pada tahun pelaporan ini.' : 'Tahun pelaporan belum diisi. Lengkapi pada step Kapasitas &amp; Utilisasi sebagai acuan konsumsi energi dan air.'">
        </p>
    </div>
    <x-button variant="secondary" ::disabled="busy" @click="backTo(2)"
        class="px-4! py-2! sm:min-h-11! sm:py-2! transition-none! enabled:active:opacity-100! disabled:cursor-wait! disabled:hover:bg-neutral-100">Ubah
        tahun pelaporan</x-button>
</div>
<section aria-labelledby="dc-energy-title" class="space-y-4 rounded-xl border border-neutral-300 p-4 sm:p-6">
    <h3 id="dc-energy-title" class="text-base font-semibold">Energi</h3>
    <div class="grid gap-4 md:grid-cols-2">
        @foreach (['it_energy', 'total_energy'] as $key)
            @php([$label, $type, $unit, $placeholder] = $energyFields[$key])
            @include('livewire.pages.data-centers.tambah-data-center.field', [
                'field' => [
                    'model' => 'form.' . $key,
                    'label' => $label,
                    'type' => $type,
                    'unit' => $unit,
                    'placeholder' => $placeholder,
                    'live' => true,
                ],
            ])
        @endforeach
    </div>
    <div class="rounded-lg bg-neutral-100/60 p-4">
        <p id="dc-pue-label" class="text-sm font-medium">Power Usage Effectiveness (PUE)</p>
        <output aria-labelledby="dc-pue-label" aria-live="polite"
            class="mt-2 block text-2xl font-semibold text-primary tabular-nums" x-text="display(pue)"></output>
        <div class="mt-3 space-y-1 border-l-[3px] border-primary bg-neutral-100 p-3 text-sm">
            <h4 class="font-medium">Rumus Perhitungan PUE</h4>
            <p>PUE = Total Energi Listrik / Energi Listrik IT</p>
            <p class="text-neutral-700">PUE menunjukkan rasio penggunaan energi keseluruhan pusat data terhadap energi
                perangkat IT.</p>
        </div>
    </div>
</section>
<section aria-labelledby="dc-water-title" class="space-y-4 rounded-xl border border-neutral-300 p-4 sm:p-6">
    <h3 id="dc-water-title" class="text-base font-semibold">Air</h3>
    <div class="grid gap-4 md:grid-cols-2">
        @php([$label, $type, $unit, $placeholder] = $energyFields['water'])
        @include('livewire.pages.data-centers.tambah-data-center.field', [
            'field' => [
                'model' => 'form.water',
                'label' => $label,
                'type' => $type,
                'unit' => $unit,
                'placeholder' => $placeholder,
                'live' => true,
            ],
        ])
        <div>
            <label for="dc-it-energy-reference" class="mb-1 block text-sm font-medium">Konsumsi Energi Listrik
                IT</label>
            <div
                class="flex min-h-11 rounded-lg border border-neutral-300 bg-neutral-100 focus-within:outline-2 focus-within:outline-primary">
                <input id="dc-it-energy-reference" readonly :value="form.it_energy" placeholder="Dari section Energi"
                    aria-describedby="dc-it-energy-reference-unit"
                    class="min-w-0 flex-1 cursor-default rounded-l-lg bg-transparent px-3 py-2 text-sm text-neutral-700 tabular-nums focus:outline-none">
                <span id="dc-it-energy-reference-unit"
                    class="flex min-w-14 shrink-0 select-none items-center justify-center rounded-r-lg border-l border-neutral-300 px-3 text-sm text-neutral-700">kWh</span>
            </div>
            <p class="mt-1 text-xs text-neutral-700">Mengikuti nilai pada section Energi; tidak perlu diisi ulang.</p>
        </div>
    </div>
    <div class="rounded-lg bg-neutral-100/60 p-4">
        <p id="dc-wue-label" class="text-sm font-medium">Water Usage Effectiveness (WUE)</p>
        <output aria-labelledby="dc-wue-label" aria-live="polite"
            class="mt-2 block text-2xl font-semibold text-primary tabular-nums" x-text="display(wue, 'L/kWh')"></output>
        <div class="mt-3 space-y-1 border-l-[3px] border-primary bg-neutral-100 p-3 text-sm">
            <h4 class="font-medium">Rumus Perhitungan WUE</h4>
            <p>WUE = Total Konsumsi Air (Liter) / Energi Listrik IT (kWh)</p>
            <p class="text-neutral-700">WUE menunjukkan jumlah air yang digunakan untuk setiap kWh energi yang
                dikonsumsi perangkat IT.</p>
        </div>
    </div>
</section>
