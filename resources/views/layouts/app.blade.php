<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIES') }} — Boshqaruv</title>

    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }
    </style>
</head>

<body class="antialiased bg-gray-50 text-gray-900">

    <div class="min-h-screen flex flex-col">

        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white border-b border-gray-200 shadow-sm">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex items-center gap-3">
                    <div class="w-1 h-6 bg-teal-600 rounded-full"></div>
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 bg-white py-4 mt-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <p class="text-xs text-gray-400">
                    &copy; {{ date('Y') }} SamISI — Samarqand Iqtisodiyot va Servis Instituti
                </p>
                <a href="/" class="text-xs text-teal-600 hover:text-teal-800 font-medium transition-colors">
                    Asosiy saytga o'tish →
                </a>
            </div>
        </footer>
    </div>

    @stack('modals')

    @livewireScriptConfig
</body>

</html>
