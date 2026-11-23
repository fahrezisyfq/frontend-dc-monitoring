<aside
    id="main-navigation"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-neutral-300 bg-white transition-transform duration-200 ease-out lg:translate-x-0"
    aria-label="Navigasi utama"
>
    <div class="flex h-16 shrink-0 items-center gap-2 border-b border-neutral-300 px-3">
        <img src="{{ asset('images/djid-mark-color.png') }}" alt="Direktorat Jenderal Infrastruktur Digital" class="h-auto max-h-7 w-20 shrink-0 object-contain">
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold leading-tight text-neutral-900">DC Monitoring</p>
            <p class="mt-0.5 text-[9px] leading-tight text-neutral-500">Sistem pemantauan data center</p>
        </div>
        <button type="button" @click="sidebarOpen = false" class="rounded-md p-2 text-neutral-700 hover:bg-neutral-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary lg:hidden" aria-label="Tutup navigasi">
            <svg viewBox="0 0 24 24" fill="none" class="size-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="m18 6-12 12M6 6l12 12" /></svg>
        </button>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-6" aria-label="Menu aplikasi">
        <div class="space-y-1">
            <a wire:navigate href="{{ route('dashboard') }}" @click="sidebarOpen = false" @class([
                'flex min-h-11 items-center gap-3 rounded-lg px-3 text-sm font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary',
                'bg-primary/10 text-primary' => request()->routeIs('dashboard'),
                'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900' => ! request()->routeIs('dashboard'),
            ]) @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="1.5" /><rect x="13" y="3" width="8" height="5" rx="1.5" /><rect x="13" y="10" width="8" height="11" rx="1.5" /><rect x="3" y="13" width="8" height="8" rx="1.5" /></svg>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="space-y-2">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-neutral-500">Manajemen Data</p>
            <a wire:navigate href="{{ route('companies.index') }}" @click="sidebarOpen = false" @class([
                'flex min-h-11 items-center gap-3 rounded-lg px-3 text-sm font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary',
                'bg-primary/10 text-primary' => request()->routeIs('companies.*'),
                'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900' => ! request()->routeIs('companies.*'),
            ]) @if (request()->routeIs('companies.*')) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4" /><path d="M9 9v.01M9 12v.01M9 15v.01M9 18v.01M15 12v.01M15 15v.01M15 18v.01" /></svg>
                <span>Perusahaan</span>
            </a>
            @foreach (['area-types.index' => 'Kawasan', 'service-types.index' => 'Service'] as $menuRoute => $menuLabel)
                <a wire:navigate href="{{ route($menuRoute) }}" @click="sidebarOpen = false" @class([
                    'flex min-h-11 items-center gap-3 rounded-lg px-3 text-sm font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary',
                    'bg-primary/10 text-primary' => request()->routeIs($menuRoute),
                    'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900' => ! request()->routeIs($menuRoute),
                ]) @if (request()->routeIs($menuRoute)) aria-current="page" @endif>
                    <svg viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        @if ($menuRoute === 'area-types.index')
                            <path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Zm6-3v15m6-12v15" />
                        @else
                            <rect x="3" y="3" width="7" height="7" rx="1" /><rect x="14" y="3" width="7" height="7" rx="1" /><rect x="3" y="14" width="7" height="7" rx="1" /><rect x="14" y="14" width="7" height="7" rx="1" />
                        @endif
                    </svg>
                    <span>{{ $menuLabel }}</span>
                </a>
            @endforeach
            @php
                $dataCenterActive = request()->routeIs('data-centers.*');
            @endphp
            <div x-data="{ dataCenterOpen: @js($dataCenterActive) }">
                <button type="button" @click="dataCenterOpen = ! dataCenterOpen" aria-controls="data-center-submenu" aria-expanded="{{ $dataCenterActive ? 'true' : 'false' }}" :aria-expanded="dataCenterOpen" @class([
                    'flex min-h-11 w-full items-center gap-3 rounded-lg px-3 text-left text-sm font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary',
                    'bg-primary/10 text-primary' => $dataCenterActive,
                    'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900' => ! $dataCenterActive,
                ])>
                    <svg viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="3" width="16" height="7" rx="2" /><rect x="4" y="14" width="16" height="7" rx="2" /><path d="M8 6.5h.01M8 17.5h.01M12 6.5h4M12 17.5h4" /></svg>
                    <span class="flex-1">Pusat Data</span>
                    <svg viewBox="0 0 24 24" fill="none" class="size-4 shrink-0 transition-transform duration-200 motion-reduce:transition-none lucide lucide-chevron-right" :class="{ 'rotate-90': dataCenterOpen }" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                </button>
                <div id="data-center-submenu" x-cloak x-show="dataCenterOpen" x-collapse.duration.200ms>
                    <div class="space-y-1 pt-1 pl-8">
                        @foreach (['data-centers.create' => 'Tambah Pusat Data', 'data-centers.index' => 'Daftar Pusat Data'] as $submenuRoute => $submenuLabel)
                            <a wire:navigate href="{{ route($submenuRoute) }}" @click="sidebarOpen = false" @class([
                                'flex min-h-11 items-center rounded-lg px-3 text-sm font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary',
                                'bg-primary/10 text-primary' => request()->routeIs($submenuRoute),
                                'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900' => ! request()->routeIs($submenuRoute),
                            ]) @if (request()->routeIs($submenuRoute)) aria-current="page" @endif>{{ $submenuLabel }}</a>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <div class="space-y-2">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-neutral-500">Administrasi</p>
            <a wire:navigate href="{{ route('users.index') }}" @click="sidebarOpen = false" @class([
                'flex min-h-11 items-center gap-3 rounded-lg px-3 text-sm font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary',
                'bg-primary/10 text-primary' => request()->routeIs('users.*'),
                'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900' => ! request()->routeIs('users.*'),
            ]) @if (request()->routeIs('users.*')) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="10" cy="7" r="4" /><path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" /></svg>
                <span>Pengguna</span>
            </a>
            <a wire:navigate href="{{ route('activity-logs.index') }}" @click="sidebarOpen = false" @class([
                'flex min-h-11 items-center gap-3 rounded-lg px-3 text-sm font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary',
                'bg-primary/10 text-primary' => request()->routeIs('activity-logs.*'),
                'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900' => ! request()->routeIs('activity-logs.*'),
            ]) @if (request()->routeIs('activity-logs.*')) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" fill="none" class="size-5 shrink-0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>
                <span>Log Aktivitas</span>
            </a>
            <a wire:navigate href="{{ route('trash.index') }}" @click="sidebarOpen = false" @class([
                'flex min-h-11 items-center gap-3 rounded-lg px-3 text-sm font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary',
                'bg-primary/10 text-primary' => request()->routeIs('trash.*'),
                'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900' => ! request()->routeIs('trash.*'),
            ]) @if (request()->routeIs('trash.*')) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" fill="none" class="size-5 shrink-0 lucide lucide-trash-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2M10 11v6M14 11v6" /></svg>
                <span>Trash</span>
            </a>
        </div>
    </nav>

    @include('layouts.partials.account-menu')
</aside>
