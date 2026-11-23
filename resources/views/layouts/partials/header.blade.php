<header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-neutral-300 bg-white px-4 sm:px-6 lg:px-8">
    <button type="button" @click="sidebarOpen = true" class="inline-flex size-11 items-center justify-center rounded-lg text-neutral-700 hover:bg-neutral-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary lg:hidden" aria-label="Buka navigasi" aria-controls="main-navigation">
        <svg viewBox="0 0 24 24" fill="none" class="size-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
    </button>
    @php
        $breadcrumbGroup = match (true) {
            request()->routeIs('companies.*', 'data-centers.*', 'area-types.*', 'service-types.*', 'providers.*') => 'Manajemen Data',
            request()->routeIs('users.*', 'activity-logs.*', 'trash.*') => 'Administrasi',
            default => null,
        };
    @endphp
    <nav aria-label="Breadcrumb" class="min-w-0 text-sm text-neutral-700">
        <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
            @if ($breadcrumbGroup)
                <li>{{ $breadcrumbGroup }}</li>
                <li aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" class="size-4 text-neutral-500 lucide lucide-chevron-right" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6" /></svg></li>
            @endif
            @if (request()->routeIs('data-centers.*'))
                <li>Pusat Data</li>
                <li aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" class="size-4 text-neutral-500 lucide lucide-chevron-right" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6" /></svg></li>
            @endif
            <li aria-current="page" class="font-medium text-neutral-900">{{ $title }}</li>
        </ol>
    </nav>
</header>
