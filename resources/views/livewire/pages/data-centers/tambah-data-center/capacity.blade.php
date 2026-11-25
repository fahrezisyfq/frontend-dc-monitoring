<section aria-labelledby="dc-capacity-title" class="space-y-4">
    <h3 id="dc-capacity-title" class="text-base font-semibold">Kapasitas &amp; Utilisasi</h3>
    <div class="grid gap-4 md:grid-cols-2">
        @foreach (['design_load', 'used_load'] as $key)
            @php([$label, $type, $unit, $placeholder] = $capacityFields[$key])
            @include('livewire.pages.data-centers.tambah-data-center.field', [
                'field' => [
                    'model' => 'form.' . $key,
                    'label' => $label,
                    'type' => $type,
                    'unit' => $unit,
                    'placeholder' => $placeholder,
                ],
            ])
        @endforeach
    </div>
</section>

<section aria-labelledby="dc-energy-title" class="space-y-4 border-t border-neutral-300 pt-6">
    <h3 id="dc-energy-title" class="text-base font-semibold">Energi dan Air</h3>
    <div class="grid gap-4 md:grid-cols-2">
        @foreach (['it_energy', 'total_energy', 'pjbtl', 'water'] as $key)
            @php([$label, $type, $unit, $placeholder] = $capacityFields[$key])
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
</section>

<section aria-labelledby="dc-pue-title" class="space-y-4 rounded-xl border border-neutral-300 p-4 sm:p-6">
    <div>
        <h3 id="dc-pue-title" class="text-base font-semibold">Power Usage Effectiveness (PUE)</h3>
        <p class="mt-1 text-sm text-neutral-700">Dihitung otomatis dari konsumsi energi listrik total dan energi
            listrik IT.</p>
    </div>
    <div class="rounded-lg bg-neutral-100/60 p-4">
        <p id="dc-pue-label" class="text-sm font-medium">Nilai PUE</p>
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

<section aria-labelledby="dc-wue-title" class="space-y-4 rounded-xl border border-neutral-300 p-4 sm:p-6">
    <div>
        <h3 id="dc-wue-title" class="text-base font-semibold">Water Usage Effectiveness (WUE)</h3>
        <p class="mt-1 text-sm text-neutral-700">Dihitung otomatis dari konsumsi air dan energi listrik IT.</p>
    </div>
    <div class="rounded-lg bg-neutral-100/60 p-4">
        <p id="dc-wue-label" class="text-sm font-medium">Nilai WUE</p>
        <output aria-labelledby="dc-wue-label" aria-live="polite"
            class="mt-2 block text-2xl font-semibold text-primary tabular-nums" x-text="display(wue, 'm³/kWh')"></output>
        <div class="mt-3 space-y-1 border-l-[3px] border-primary bg-neutral-100 p-3 text-sm">
            <h4 class="font-medium">Rumus Perhitungan WUE</h4>
            <p>WUE = Total Konsumsi Air (m³) / Energi Listrik IT (kWh)</p>
            <p class="text-neutral-700">WUE menunjukkan jumlah air yang digunakan untuk setiap kWh energi yang
                dikonsumsi perangkat IT.</p>
        </div>
    </div>
</section>

<section aria-labelledby="dc-reporting-title" class="space-y-4 border-t border-neutral-300 pt-6">
    <div>
        <h3 id="dc-reporting-title" class="text-base font-semibold">Periode Pelaporan</h3>
        <p class="mt-1 text-sm text-neutral-700">Seluruh konsumsi energi dan air di atas mengacu pada periode
            pelaporan ini.</p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        @php([$label, $type, $unit, $placeholder] = $capacityFields['reporting_year'])
        @include('livewire.pages.data-centers.tambah-data-center.field', [
            'field' => [
                'model' => 'form.reporting_year',
                'label' => $label,
                'type' => $type,
                'unit' => $unit,
                'placeholder' => $placeholder,
            ],
        ])
    </div>
</section>
