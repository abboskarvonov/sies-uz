<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->

    @livewireStyles
    @stack('styles')
</head>

<body class="font-sans antialiased text-gray-900 dark:text-white">

    <div class="min-h-screen bg-white dark:bg-gray-900">
        @include('components.main.header')
        @include('components.main.navbar')
        <main>
            {{ $slot }}
        </main>
        @include('components.main.footer')
    </div>

    @stack('modals')
    @stack('scripts')

    @livewireScripts
</body>

</html>
