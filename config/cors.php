<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Bu fayl Laravel Sanctum bilan ishlaydi.
    | Faylni Laravel loyihasining config/cors.php ga ko'chiring.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | CORS qo'llanilhadigan URL yo'llari
    |--------------------------------------------------------------------------
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ruxsat berilgan HTTP metodlar
    |--------------------------------------------------------------------------
    */

    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Ruxsat berilgan originlar (domenlar)
    |--------------------------------------------------------------------------
    |
    | Production:
    |   https://sies.uz   — asosiy domen
    |
    | Development (Expo web / localhost):
    |   http://localhost:*
    |
    */

    'allowed_origins' => [
        // ── Production ────────────────────────────────────────────
        'https://sies.uz',
        'https://www.sies.uz',

        // ── Expo / React Native Web (local development) ───────────
        'http://localhost',
        'http://localhost:8081',   // expo start --web (default)
        'http://localhost:19006',  // expo start --web (older SDK)
        'http://localhost:3000',
        'http://localhost:5173',

        'http://127.0.0.1',
        'http://127.0.0.1:8081',
        'http://127.0.0.1:19006',
    ],

    /*
    |--------------------------------------------------------------------------
    | Wildcard pattern bilan originlar (ixtiyoriy)
    |--------------------------------------------------------------------------
    |
    | Agar yuqoridagi ro'yxat kamchilik qilsa, pastdagi pattern ishlatiladi.
    | Masalan, barcha localhost portlarga ruxsat berish uchun:
    |
    */

    'allowed_origins_patterns' => [
        '#^http://localhost(:\d+)?$#',   // http://localhost va http://localhost:XXXX
        '#^http://127\.0\.0\.1(:\d+)?$#',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ruxsat berilgan headerlar
    |--------------------------------------------------------------------------
    */

    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'Authorization',
        'Accept',
        'Accept-Language',
        'X-CSRF-TOKEN',
        'X-XSRF-TOKEN',
    ],

    /*
    |--------------------------------------------------------------------------
    | Expose qilinadigan headerlar (client tomondan o'qish mumkin)
    |--------------------------------------------------------------------------
    */

    'exposed_headers' => [
        'Content-Language',
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'Retry-After',
    ],

    /*
    |--------------------------------------------------------------------------
    | Preflight cache muddati (sekund)
    |--------------------------------------------------------------------------
    |
    | OPTIONS so'rovining natijasi shu vaqt davomida cache qilinadi.
    |
    */

    'max_age' => 86400, // 24 soat

    /*
    |--------------------------------------------------------------------------
    | Credentials (cookie, Authorization header)
    |--------------------------------------------------------------------------
    |
    | Sanctum Bearer token ishlatilganda true bo'lishi kerak.
    |
    */

    'supports_credentials' => true,

];
