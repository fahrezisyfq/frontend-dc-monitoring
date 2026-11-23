<article aria-labelledby="dashboard-map-title" data-dashboard-map
    class="min-w-0 overflow-hidden rounded-xl border border-neutral-300 bg-white">
    <div class="p-4">
        <h2 id="dashboard-map-title" class="text-base font-semibold text-neutral-900">Persebaran Pusat Data</h2>
        <p class="mt-1 text-xs text-neutral-500">Lokasi pusat data dan pembagian zona waktu Indonesia</p>
    </div>
    <div class="px-4 pb-4">
        <div data-map-canvas class="relative w-full" wire:ignore>
            <svg class="block h-auto w-full" role="img"
                aria-label="Peta provinsi Indonesia dan lokasi pusat data"></svg>
        </div>
        <script type="application/json" data-map-centers>@json($dataCenters)</script>
        <ul aria-label="Ringkasan pusat data per zona waktu" class="mt-3 grid grid-cols-1 gap-2">
            @foreach (['WIB' => 'UTC+7', 'WITA' => 'UTC+8', 'WIT' => 'UTC+9'] as $zone => $offset)
                <li
                    class="flex items-center justify-between gap-2 rounded-lg border border-neutral-300 bg-white px-3 py-2">
                    <span class="flex items-center gap-2 text-xs font-medium text-neutral-700"><span
                            class="size-2 shrink-0 rounded-full"
                            style="background-color: {{ ['WIB' => '#7FB8DC', 'WITA' => '#79C6B2', 'WIT' => '#DDBD62'][$zone] }}"></span>{{ $zone }}
                        <span class="text-xs font-normal text-neutral-500">{{ $offset }}</span></span>
                    <span class="text-xs font-semibold tabular-nums text-neutral-900"><span
                            data-zone-count="{{ $zone }}">0</span> <span
                            class="font-normal text-neutral-500">pusat data</span></span>
                </li>
            @endforeach
        </ul>
    </div>
</article>
