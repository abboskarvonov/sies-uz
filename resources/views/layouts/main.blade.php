@props([
    'metaTitle' => config('app.name'),
    'metaDescription' =>
        'Samarqand Iqtisodiyot va Servis Instituti — iqtisodiyot, buxgalteriya, bank ishi, servis, menejment va zamonaviy fanlar bo‘yicha yetakchi oliy ta’lim muassasasi. Talabalarga sifatli ta’lim, ilmiy izlanishlar va amaliyot imkoniyatlarini taqdim etadi.',
    'metaKeywords' => 'Samarqand, iqtisodiyot, universitet, o‘qish, institut',
    'metaImage' => asset('img/og-image.webp'),
    'canonical' => url()->current(),
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ html_entity_decode($metaTitle) }}</title>
    <meta name="description" content="{{ html_entity_decode($metaDescription) }}">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <link rel="canonical" href="{{ $canonical }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:site_name" content="SamISI">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
    @livewireStyles
    @stack('styles')
    <style>
        .page-header {
            background-size: cover;
            background-position: center;
            background-image: url("{{ asset('img/hero-bg.webp') }}");
        }
    </style>
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
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
    <script>
        function copyToClipboard(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast("Link nusxalandi!");
                }).catch(err => {
                    console.error('Nusxalashda xato:', err);
                    fallbackCopy(text);
                });
            } else {
                fallbackCopy(text);
            }
        }

        // Eski brauzerlar uchun fallback
        function fallbackCopy(text) {
            const input = document.createElement('input');
            input.value = text;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            showToast("Link nusxalandi!");
        }

        // Oddiy toast (alert o‘rniga chiroyliroq)
        function showToast(message) {
            let toast = document.createElement('div');
            toast.textContent = message;
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.background = '#333';
            toast.style.color = '#fff';
            toast.style.padding = '10px 15px';
            toast.style.borderRadius = '6px';
            toast.style.zIndex = '9999';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const els = document.querySelectorAll('.countup');
            if (!('IntersectionObserver' in window) || els.length === 0) return;

            const animate = el => {
                const target = parseInt(el.dataset.target || '0', 10);
                const dur = 1200; // ms
                const start = performance.now();

                const step = now => {
                    const p = Math.min(1, (now - start) / dur);
                    const val = Math.floor(target * (0.5 - Math.cos(Math.PI * p) / 2)); // easeInOut
                    el.textContent = new Intl.NumberFormat('uz-UZ').format(val);
                    if (p < 1) requestAnimationFrame(step);
                };
                requestAnimationFrame(step);
            };

            const obs = new IntersectionObserver((entries, ob) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animate(entry.target);
                        ob.unobserve(entry.target); // bir marta
                    }
                });
            }, {
                threshold: 0.3
            });

            els.forEach(el => obs.observe(el));
        });
    </script>
    @stack('modals')
    @stack('scripts')

    @livewireScripts
</body>

</html>
