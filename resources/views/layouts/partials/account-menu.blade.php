<div class="relative shrink-0 border-t border-neutral-300 px-6 py-4" x-data="{ accountOpen: false }" @click.outside="accountOpen = false" @keydown.escape.stop="accountOpen = false; $refs.accountToggle?.focus()" @focusout="if (! $el.contains($event.relatedTarget)) accountOpen = false">
    @php
        $sidebarUser = ['name' => 'Administrator', 'role' => 'Super Admin'];
    @endphp
    <button type="button" x-ref="accountToggle" @click="accountOpen = ! accountOpen" :aria-expanded="accountOpen" aria-controls="account-dropdown" class="flex min-h-11 w-full items-center gap-3 rounded-lg text-left hover:bg-neutral-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
        <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-neutral-700">
            <svg viewBox="0 0 24 24" fill="none" class="size-5 lucide lucide-user-round" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="5" /><path d="M20 21a8 8 0 0 0-16 0" /></svg>
        </span>
        <span class="min-w-0 flex-1">
            <span class="block truncate text-sm font-medium text-neutral-900" title="{{ $sidebarUser['name'] }}">{{ $sidebarUser['name'] }}</span>
            <span class="block truncate text-xs text-neutral-500">{{ $sidebarUser['role'] }}</span>
        </span>
        <svg viewBox="0 0 24 24" fill="none" class="size-4 shrink-0 text-neutral-500 lucide lucide-chevrons-up-down" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m7 15 5 5 5-5m-10-6 5-5 5 5" /></svg>
    </button>
    <div id="account-dropdown" x-cloak x-show="accountOpen" x-transition.opacity.duration.150ms class="absolute bottom-full left-4 right-4 z-10 mb-2 rounded-lg border border-neutral-300 bg-white p-1 shadow-sm motion-reduce:transition-none">
        <button type="button" class="flex min-h-11 w-full items-center gap-2 rounded-md px-3 text-sm text-neutral-700 hover:bg-neutral-100 focus-visible:outline-2 focus-visible:outline-primary">
            <svg viewBox="0 0 24 24" fill="none" class="size-4 shrink-0 lucide lucide-user-round" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="5" /><path d="M20 21a8 8 0 0 0-16 0" /></svg>
            Profil Saya
        </button>
        <button type="button" class="flex min-h-11 w-full items-center gap-2 rounded-md px-3 text-sm text-red hover:bg-neutral-100 focus-visible:outline-2 focus-visible:outline-primary">
            <svg viewBox="0 0 24 24" fill="none" class="size-4 shrink-0 lucide lucide-log-out" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m16 17 5-5-5-5M21 12H9M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /></svg>
            Keluar
        </button>
    </div>
</div>
