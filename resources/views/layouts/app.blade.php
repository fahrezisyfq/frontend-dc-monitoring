<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · DC Monitoring</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-neutral-100 font-sans text-neutral-900 antialiased">
    <div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" class="min-h-screen">
        <button x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" type="button"
            aria-label="Tutup navigasi" class="fixed inset-0 z-40 bg-neutral-900/40 lg:hidden"></button>

        @include('layouts.partials.sidebar')

        <div class="min-h-screen lg:pl-64">
            @include('layouts.partials.header', ['title' => $title])

            <main class="p-4 sm:p-6 lg:p-8" id="main-content">
                <div class="mx-auto max-w-7xl">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    @livewireScripts
</body>

</html>
