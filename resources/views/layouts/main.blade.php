@props([
    'metaTitle' => config('app.name'),
    'metaDescription' => "Samarqand Iqtisodiyot va Servis Instituti — iqtisodiyot, buxgalteriya, bank ishi, servis, menejment va zamonaviy fanlar bo'yicha yetakchi oliy ta'lim muassasasi. Talabalarga sifatli ta'lim, ilmiy izlanishlar va amaliyot imkoniyatlarini taqdim etadi.",
    'metaKeywords' => "Samarqand, iqtisodiyot, universitet, o'qish, institut",
    'metaImage' => asset('img/og-image.webp'),
    'canonical' => url()->current(),
])

@php
    $ogLocaleMap = ['uz' => 'uz_UZ', 'ru' => 'ru_RU', 'en' => 'en_US'];
    $currentLocale = app()->getLocale();
    $ogLocale = $ogLocaleMap[$currentLocale] ?? 'uz_UZ';

    // Ensure metaImage is absolute URL with https
    $ogImage = $metaImage;
    if ($ogImage && !str_starts_with($ogImage, 'http')) {
        $ogImage = asset($ogImage);
    }
    $ogImage = str_replace('http://', 'https://', $ogImage);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ html_entity_decode($metaTitle) }}</title>
    <meta name="description" content="{{ html_entity_decode($metaDescription) }}">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <link rel="canonical" href="{{ $canonical }}">

    @php use Mcamara\LaravelLocalization\Facades\LaravelLocalization; @endphp
    @foreach(['uz', 'ru', 'en'] as $hrefLocale)
    <link rel="alternate" hreflang="{{ $hrefLocale }}" href="{{ LaravelLocalization::getLocalizedURL($hrefLocale, null, [], true) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ LaravelLocalization::getLocalizedURL('uz', null, [], true) }}">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ $ogLocale }}">
    @foreach(array_diff_key($ogLocaleMap, [$currentLocale => '']) as $altLocale)
    <meta property="og:locale:alternate" content="{{ $altLocale }}">
    @endforeach
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:site_name" content="SamISI">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <!-- Fonts - Optimized for performance -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

    <!-- Preload critical font -->
    <link rel="preload" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"></noscript>

    <!-- Preload LCP image for faster rendering -->
    <link rel="preload" as="image" href="{{ asset('img/hero-bg-1920.webp') }}"
          imagesrcset="{{ asset('img/hero-bg-640.webp') }} 640w, {{ asset('img/hero-bg-1280.webp') }} 1280w, {{ asset('img/hero-bg-1920.webp') }} 1920w"
          imagesizes="100vw" fetchpriority="high">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @stack('styles')
    <style>
        .page-header {
            background-size: cover;
            background-position: center;
            background-image: image-set(
                url("{{ asset('img/hero-bg-640.webp') }}") 1x,
                url("{{ asset('img/hero-bg-1280.webp') }}") 2x,
                url("{{ asset('img/hero-bg-1920.webp') }}") 3x
            );
            /* Fallback for older browsers */
            background-image: url("{{ asset('img/hero-bg-1280.webp') }}");
        }

        /* Responsive background images */
        @media (max-width: 640px) {
            .page-header {
                background-image: url("{{ asset('img/hero-bg-640.webp') }}");
            }
        }

        @media (min-width: 641px) and (max-width: 1280px) {
            .page-header {
                background-image: url("{{ asset('img/hero-bg-1280.webp') }}");
            }
        }

        @media (min-width: 1281px) {
            .page-header {
                background-image: url("{{ asset('img/hero-bg-1920.webp') }}");
            }
        }
    </style>
</head>

<body class="font-sans antialiased text-gray-900 dark:text-white">

    <div class="min-h-screen bg-white dark:bg-gray-900">
        @include('components.main.header')
        @include('components.main.navbar')
        @include('components.main.quick-links')
        <main>
            {{ $slot }}
        </main>
        @include('components.main.footer')
    </div>
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

        // Oddiy toast (alert o'rniga chiroyliroq)
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

    <!-- Lazy-load Google Maps -->
    <script>
    (function(){
        var mc = document.getElementById('map-container');
        if (!mc) return;
        var ob = new IntersectionObserver(function(e) {
            if (e[0].isIntersecting) {
                var f = document.createElement('iframe');
                f.src = mc.dataset.mapSrc;
                f.width = '100%';
                f.height = '100%';
                f.title = 'SamISI Location';
                f.allowFullscreen = true;
                mc.innerHTML = '';
                mc.appendChild(f);
                ob.disconnect();
            }
        }, {rootMargin: '200px'});
        ob.observe(mc);
    })();
    </script>

    <!-- Google Analytics - loaded after page content -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-136882406-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag("js", new Date());
        gtag("config", "UA-136882406-1");
    </script>
</body>

</html>
