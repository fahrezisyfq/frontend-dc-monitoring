<article data-dashboard-trend class="flex min-w-0 flex-col rounded-xl border border-neutral-300 bg-white p-4">
    <div class="mb-3">
        <h2 class="text-base font-semibold text-neutral-900">Perkembangan Pusat Data</h2>
        <p class="mt-1 text-xs text-neutral-500">Jumlah pusat data kumulatif per tahun</p>
    </div>
    <script type="application/json" data-trend-source>@json($yearlyCounts)</script>
    <div data-trend-content wire:ignore class="flex flex-1 items-center py-2">
        <div data-trend-canvas class="relative w-full">
            <p data-trend-empty role="status" class="hidden py-8 text-center text-sm text-neutral-700">Belum ada data
                perkembangan pusat data.</p>
            <div data-trend-tooltip role="tooltip"
                class="pointer-events-none absolute top-0 left-1/2 z-10 hidden w-max max-w-full -translate-x-1/2 rounded-lg border border-neutral-300 bg-white px-3 py-2 text-center text-sm text-neutral-900 shadow-sm">
            </div>
        </div>
    </div>
</article>
