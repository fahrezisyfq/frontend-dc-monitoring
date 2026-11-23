<x-drawer name="company-detail" title="Detail Perusahaan" size="lg" wire:key="company-detail-drawer">
    <div class="space-y-4">
        <section aria-label="Informasi perusahaan">
            <h3 class="text-sm font-semibold">Informasi Perusahaan</h3>
            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="min-w-0">
                    <h4 class="text-xs font-semibold">Identitas Perusahaan</h4>
                    <dl class="mt-3 space-y-2.5">
                        <div>
                            <dt class="text-xs text-neutral-700">Nama Perusahaan</dt>
                            <dd class="mt-1 font-medium wrap-anywhere"
                                x-text="$wire.companies[selectedId]?.company_name ?? ''"></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-neutral-700">NIB</dt>
                            <dd class="mt-1 tabular-nums wrap-anywhere" x-text="$wire.companies[selectedId]?.nib ?? ''">
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-neutral-700">Alamat Perusahaan</dt>
                            <dd class="mt-1 whitespace-pre-line wrap-anywhere"
                                x-text="$wire.companies[selectedId]?.company_address ?? ''"></dd>
                        </div>
                    </dl>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-semibold">Informasi Kontak</h4>
                    <dl class="mt-3 space-y-2.5">
                        <div>
                            <dt class="text-xs text-neutral-700">Nama Kontak</dt>
                            <dd class="mt-1 wrap-anywhere" x-text="$wire.companies[selectedId]?.contact_name ?? ''">
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-neutral-700">Telepon</dt>
                            <dd class="mt-1 tabular-nums wrap-anywhere"
                                x-text="$wire.companies[selectedId]?.contact_phone ?? ''"></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-neutral-700">Email</dt>
                            <dd class="mt-1 wrap-anywhere" x-text="$wire.companies[selectedId]?.contact_email ?? ''">
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>
        <section class="border-t border-neutral-300 pt-4" aria-label="Data Center terdaftar">
            <h3 class="text-sm font-semibold">Data Center Terdaftar</h3>
            <p x-show="!($wire.companyDataCenters[selectedId] ?? []).length" class="mt-3 text-neutral-700">Belum ada
                pusat data yang terdaftar.</p>
            <ul class="mt-3 space-y-2">
                <template x-for="(center, index) in ($wire.companyDataCenters[selectedId] ?? [])"
                    :key="`${selectedId}-${index}`">
                    <li class="space-y-2 rounded-lg border border-neutral-300 p-3">
                        <h4 class="font-medium wrap-anywhere" x-text="center.name"></h4>
                        <p class="text-xs text-neutral-700 wrap-anywhere" x-text="center.city"></p>
                        <div>
                            @foreach (['Operational' => 'primary', 'Construction' => 'info', 'Planning' => 'warning', 'Decommissioned' => 'neutral'] as $status => $variant)
                                <template x-if="center.status === @js($status)">
                                    <x-badge :variant="$variant">
                                        {{ $status }}
                                    </x-badge>
                                </template>
                            @endforeach
                        </div>
                    </li>
                </template>
            </ul>
        </section>
    </div>
</x-drawer>
