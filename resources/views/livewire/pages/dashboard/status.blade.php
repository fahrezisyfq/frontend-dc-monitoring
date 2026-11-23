<article data-dashboard-status class="min-w-0 rounded-xl border border-neutral-300 bg-white p-4">
    <div class="mb-3">
        <h2 class="text-base font-semibold text-neutral-900">Status Pusat Data</h2>
        <p class="mt-1 text-xs text-neutral-500">Distribusi berdasarkan status</p>
    </div>
    <script type="application/json" data-status-counts>@json($statusCounts)</script>
    <div data-status-chart wire:ignore class="relative flex flex-wrap items-center justify-center gap-4">
        <div data-status-canvas class="aspect-square w-40 max-w-full shrink-0"></div>
        <p data-status-empty class="hidden w-full text-center text-sm text-neutral-700" role="status">Belum ada pusat data untuk ditampilkan.</p>
        <ul data-status-legend aria-label="Jumlah pusat data per status" class="grid w-full max-w-64 grid-cols-[minmax(0,1fr)_auto] gap-x-3 gap-y-2"></ul>
        <div data-status-tooltip role="tooltip" class="pointer-events-none absolute top-0 left-1/2 z-10 hidden w-max max-w-full -translate-x-1/2 rounded-lg border border-neutral-300 bg-white px-3 py-2 text-center text-sm text-neutral-900 shadow-sm"></div>
    </div>
</article>
