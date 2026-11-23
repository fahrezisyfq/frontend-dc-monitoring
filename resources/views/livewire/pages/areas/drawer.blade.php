<x-drawer name="area-detail" title="Detail Kawasan" size="lg" wire:key="area-detail-drawer">
    <div class="space-y-4">
        <section>
            <h3 class="mb-3 text-sm font-semibold">Informasi Kawasan</h3>
            <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="min-w-0">
                    <dt class="text-xs text-neutral-700">Nama Kawasan</dt>
                    <dd class="mt-1 wrap-anywhere"><span x-text="$wire.areas[selectedId]?.name || '-'"></span></dd>
                </div>
                <div class="min-w-0">
                    <dt class="text-xs text-neutral-700">Kategori Kawasan</dt>
                    <dd class="mt-1 wrap-anywhere">
                        @foreach (['KEK' => 'primary', 'Kawasan Industri' => 'info', 'NON Kawasan' => 'neutral'] as $category => $variant)
                            <template x-if="$wire.areas[selectedId]?.category === @js($category)"><x-badge
                                    :variant="$variant">{{ $category }}</x-badge></template>
                        @endforeach
                    </dd>
                </div>
                <div class="min-w-0">
                    <dt class="text-xs text-neutral-700">Total Luas Kawasan</dt>
                    <dd class="mt-1 wrap-anywhere"><span
                            x-text="$wire.areas[selectedId]?.total_area !== null && $wire.areas[selectedId]?.total_area !== undefined && $wire.areas[selectedId]?.total_area !== '' ? new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format($wire.areas[selectedId]?.total_area) + ' ha' : '-'"></span>
                    </dd>
                </div>
            </dl>
        </section>
        <section class="border-t border-neutral-300 pt-4">
            <h3 class="mb-3 text-sm font-semibold">Infrastruktur Kawasan</h3>
            <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="min-w-0">
                    <dt class="text-xs text-neutral-700">Desalinasi</dt>
                    <dd class="mt-1 wrap-anywhere"><template
                            x-if="$wire.areas[selectedId]?.desalination === '1'"><x-badge
                                variant="primary">Ya</x-badge></template><template
                            x-if="$wire.areas[selectedId]?.desalination === '0'"><x-badge
                                variant="neutral">Tidak</x-badge></template></dd>
                </div>
                <div class="min-w-0" x-show="$wire.areas[selectedId]?.desalination === '1'">
                    <dt class="text-xs text-neutral-700">Debit Desalinasi</dt>
                    <dd class="mt-1 wrap-anywhere"><span
                            x-text="$wire.areas[selectedId]?.desalination_flow !== null && $wire.areas[selectedId]?.desalination_flow !== undefined && $wire.areas[selectedId]?.desalination_flow !== '' ? new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format($wire.areas[selectedId]?.desalination_flow) + ' l/s' : '-'"></span>
                    </dd>
                </div>
                <div class="min-w-0">
                    <dt class="text-xs text-neutral-700">Water Treatment</dt>
                    <dd class="mt-1 wrap-anywhere"><template
                            x-if="$wire.areas[selectedId]?.water_treatment === '1'"><x-badge
                                variant="primary">Ya</x-badge></template><template
                            x-if="$wire.areas[selectedId]?.water_treatment === '0'"><x-badge
                                variant="neutral">Tidak</x-badge></template></dd>
                </div>
                <div class="min-w-0">
                    <dt class="text-xs text-neutral-700">Gardu Induk</dt>
                    <dd class="mt-1 wrap-anywhere"><span
                            x-text="$wire.areas[selectedId]?.substations !== null && $wire.areas[selectedId]?.substations !== undefined && $wire.areas[selectedId]?.substations !== '' ? new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format($wire.areas[selectedId]?.substations) + '' : '-'"></span>
                    </dd>
                </div>
                <div class="min-w-0">
                    <dt class="text-xs text-neutral-700">Electricity Number</dt>
                    <dd class="mt-1 wrap-anywhere"><span
                            x-text="$wire.areas[selectedId]?.electricity_number !== null && $wire.areas[selectedId]?.electricity_number !== undefined && $wire.areas[selectedId]?.electricity_number !== '' ? new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format($wire.areas[selectedId]?.electricity_number) + '' : '-'"></span>
                    </dd>
                </div>
                <div class="min-w-0">
                    <dt class="text-xs text-neutral-700">Cable Landing Station</dt>
                    <dd class="mt-1 wrap-anywhere"><span
                            x-text="$wire.areas[selectedId]?.cable_landing_station || '-'"></span></dd>
                </div>
            </dl>
        </section>
        <section class="border-t border-neutral-300 pt-4">
            <h3 class="mb-3 text-sm font-semibold">Informasi Provider</h3>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <template x-for="id in ($wire.areas[selectedId]?.provider_ids ?? [])" :key="id">
                    <div class="min-w-0 rounded-lg border border-neutral-300 p-3">
                        <p class="font-medium wrap-anywhere" x-text="$wire.providers[id]?.name ?? '-'"></p>
                        <p class="mt-1 text-xs text-neutral-700 wrap-anywhere"
                            x-text="$wire.providers[id]?.service ?? '-'"></p>
                    </div>
                </template>
            </div>
            <p x-show="!($wire.areas[selectedId]?.provider_ids ?? []).length" class="text-xs text-neutral-700">Belum ada
                provider terdaftar</p>
        </section>
        <section class="border-t border-neutral-300 pt-4">
            <h3 class="mb-3 text-sm font-semibold">Informasi Kontak</h3>
            <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="min-w-0">
                    <dt class="text-xs text-neutral-700">Nama Kontak</dt>
                    <dd class="mt-1 wrap-anywhere"><span x-text="$wire.areas[selectedId]?.contact_name || '-'"></span>
                    </dd>
                </div>
                <div class="min-w-0">
                    <dt class="text-xs text-neutral-700">Email</dt>
                    <dd class="mt-1 wrap-anywhere"><span x-text="$wire.areas[selectedId]?.email || '-'"></span></dd>
                </div>
                <div class="min-w-0">
                    <dt class="text-xs text-neutral-700">Phone</dt>
                    <dd class="mt-1 wrap-anywhere"><span x-text="$wire.areas[selectedId]?.phone || '-'"></span></dd>
                </div>
            </dl>
        </section>
    </div>
</x-drawer>
