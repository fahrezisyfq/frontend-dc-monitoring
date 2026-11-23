<section aria-label="Ringkasan pusat data" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <article
        class="flex min-h-28 min-w-0 gap-3 items-center justify-between rounded-xl border border-neutral-300 bg-white p-4">
        <div>
            <h2 class="text-xs font-medium text-neutral-700">Total Perusahaan</h2>
            <p class="mt-2 tabular-nums text-2xl font-semibold tracking-tight text-neutral-900">{{ $companyCount }}</p>
            <p class="mt-1 text-xs text-neutral-500">Perusahaan terdaftar</p>
        </div>
        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
            aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" class="size-4" stroke="currentColor" stroke-width="1.7"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4" />
                <path d="M9 9v.01M9 12v.01M9 15v.01M9 18v.01M15 12v.01M15 15v.01M15 18v.01" />
            </svg>
        </span>
    </article>

    <article
        class="flex min-h-28 min-w-0 gap-3 items-center justify-between rounded-xl border border-neutral-300 bg-white p-4">
        <div>
            <h2 class="text-xs font-medium text-neutral-700">Total Data Center</h2>
            <p class="mt-2 tabular-nums text-2xl font-semibold tracking-tight text-neutral-900">{{ $dataCenterCount }}
            </p>
            <p class="mt-1 text-xs text-neutral-500">Semua status</p>
        </div>
        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-cyan/15 text-primary"
            aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" class="size-4" stroke="currentColor" stroke-width="1.7"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="3" width="16" height="7" rx="2" />
                <rect x="4" y="14" width="16" height="7" rx="2" />
                <path d="M8 6.5h.01M8 17.5h.01M12 6.5h4M12 17.5h4" />
            </svg>
        </span>
    </article>

    <article
        class="flex min-h-28 min-w-0 gap-3 items-center justify-between rounded-xl border border-neutral-300 bg-white p-4 sm:col-span-2 xl:col-span-1">
        <div>
            <h2 class="text-xs font-medium text-neutral-700">Total Kapasitas IT Load</h2>
            <p class="mt-2 tabular-nums text-2xl font-semibold tracking-tight text-neutral-900">{{ $totalCapacity }}
                <span class="text-sm font-medium text-neutral-700">MW</span></p>
            <p class="mt-1 text-xs text-neutral-500">Tidak termasuk decommissioned</p>
        </div>
        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-yellow/20 text-neutral-900"
            aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" class="size-4" stroke="currentColor" stroke-width="1.7"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M13 2 4 14h7l-1 8 10-12h-7l1-8Z" />
            </svg>
        </span>
    </article>
</section>
