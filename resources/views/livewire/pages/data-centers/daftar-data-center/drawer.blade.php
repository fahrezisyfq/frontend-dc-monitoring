@php($statusVariants = ['Operational' => 'primary', 'Construction' => 'info', 'Planning' => 'warning', 'Decommissioned' => 'neutral'])

<x-drawer name="data-center-detail" title="Detail Pusat Data" size="xl" wire:key="data-center-detail-drawer">
    <div x-data="{
        dcDetail(value, unit = '') {
            if (value === '' || value === null || value === undefined) return '—';
            return String(value) + (unit ? ' ' + unit : '');
        }
    }" class="space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-neutral-300 pb-4">
            <div class="min-w-0">
                <h3 class="text-base font-semibold wrap-anywhere"
                    x-text="$wire.dataCenters[selectedId]?.form?.name ?? ''"></h3>
                <p class="mt-1 text-xs text-neutral-700 wrap-anywhere"
                    x-text="[ $wire.dataCenters[selectedId]?.company_name, $wire.dataCenters[selectedId]?.company_nib ].filter(Boolean).join(' · ')">
                </p>
            </div>
            @foreach ($statusVariants as $status => $variant)
                <template x-if="$wire.dataCenters[selectedId]?.form?.status === @js($status)">
                    <x-badge :variant="$variant">{{ $status }}</x-badge>
                </template>
            @endforeach
        </div>

        <section class="space-y-3" aria-label="Identitas pusat data">
            <h4 class="text-sm font-semibold">Identitas Pusat Data</h4>
            <dl class="grid gap-4 sm:grid-cols-2">
                @include('livewire.pages.data-centers.daftar-data-center.detail-value', [
                    'label' => 'Perusahaan',
                    'expression' => '$wire.dataCenters[selectedId]?.company_name',
                ])
                @include('livewire.pages.data-centers.daftar-data-center.detail-value', [
                    'label' => 'NIB',
                    'expression' => '$wire.dataCenters[selectedId]?.company_nib',
                ])
                @foreach ($mainFields as $key => [$label])
                    @include('livewire.pages.data-centers.daftar-data-center.detail-value', [
                        'label' => $label,
                        'expression' => '$wire.dataCenters[selectedId]?.form?.' . $key,
                    ])
                @endforeach
                @foreach (['status' => 'Status Pusat Data', 'province' => 'Provinsi', 'city' => 'Kota/Kabupaten', 'district' => 'Kecamatan', 'pum' => 'Kode PUM (simulasi)', 'timezone' => 'Zona Waktu', 'ai_ready' => 'AI Ready', 'area' => 'Kawasan'] as $key => $label)
                    @php($expression = in_array($key, ['pum', 'timezone'], true) ? '$wire.dataCenters[selectedId]?.' . $key : '$wire.dataCenters[selectedId]?.form?.' . $key)
                    @include('livewire.pages.data-centers.daftar-data-center.detail-value', [
                        'label' => $label,
                        'expression' => $expression,
                    ])
                @endforeach
                @include('livewire.pages.data-centers.daftar-data-center.detail-value', [
                    'label' => 'Service Models',
                    'expression' => '($wire.dataCenters[selectedId]?.form?.services ?? []).join(\', \')',
                ])
            </dl>
        </section>

        <section class="space-y-3 border-t border-neutral-300 pt-4" aria-label="Kapasitas dan utilisasi pusat data">
            <h4 class="text-sm font-semibold">Kapasitas &amp; Utilisasi</h4>
            <dl class="grid gap-4 sm:grid-cols-2">
                @foreach ($capacityFields as $key => [$label, $type, $unit])
                    @if ($key !== 'reporting_year')
                        @include('livewire.pages.data-centers.daftar-data-center.detail-value', [
                            'label' => $label,
                            'expression' => '$wire.dataCenters[selectedId]?.form?.' . $key,
                            'unit' => $unit,
                        ])
                    @endif
                @endforeach
            </dl>
        </section>

        <section class="space-y-3 border-t border-neutral-300 pt-4" aria-label="Energi dan air pusat data">
            <h4 class="text-sm font-semibold">Energi dan Air</h4>
            <dl class="grid gap-4 sm:grid-cols-2">
                @foreach (['reporting_year' => 'Tahun Pelaporan (Reporting Year)'] as $key => $label)
                    @include('livewire.pages.data-centers.daftar-data-center.detail-value', [
                        'label' => $label,
                        'expression' => '$wire.dataCenters[selectedId]?.form?.' . $key,
                    ])
                @endforeach
                @foreach ($energyFields as $key => [$label, $type, $unit])
                    @include('livewire.pages.data-centers.daftar-data-center.detail-value', [
                        'label' => $label,
                        'expression' => '$wire.dataCenters[selectedId]?.form?.' . $key,
                        'unit' => $unit,
                    ])
                @endforeach
                @include('livewire.pages.data-centers.daftar-data-center.detail-value', [
                    'label' => 'Power Usage Effectiveness (PUE)',
                    'expression' => '$wire.dataCenters[selectedId]?.pue',
                ])
                @include('livewire.pages.data-centers.daftar-data-center.detail-value', [
                    'label' => 'Water Usage Effectiveness (WUE)',
                    'expression' => '$wire.dataCenters[selectedId]?.wue',
                    'unit' => 'L/kWh',
                ])
            </dl>
            <p class="text-xs text-neutral-700">PUE = total energi listrik / energi listrik IT. WUE = konsumsi air /
                energi listrik IT.</p>
        </section>

        <section class="space-y-3 border-t border-neutral-300 pt-4 pb-4" aria-label="Sertifikasi pusat data">
            <h4 class="text-sm font-semibold">Sertifikasi</h4>
            <p x-show="!($wire.dataCenters[selectedId]?.certificates ?? []).length" class="text-sm text-neutral-700">
                Belum ada sertifikasi</p>
            <template x-for="(certificate, index) in ($wire.dataCenters[selectedId]?.certificates ?? [])"
                :key="`certificate-${index}`">
                <div class="space-y-3 rounded-lg border border-neutral-300 p-3">
                    <h5 class="text-sm font-semibold" x-text="'Sertifikasi ' + (index + 1)"></h5>
                    <dl class="grid gap-4 sm:grid-cols-2">
                        @foreach ($certificateFields as $key => [$label])
                            @include('livewire.pages.data-centers.daftar-data-center.detail-value', [
                                'label' => $label,
                                'expression' => 'certificate.' . $key,
                            ])
                        @endforeach
                    </dl>
                </div>
            </template>
        </section>
    </div>
</x-drawer>
