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
    <main class="flex min-h-screen items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </main>
    @livewireScripts
</body>

</html>
