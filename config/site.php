<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site Configuration
    |--------------------------------------------------------------------------
    |
    | Sayt uchun asosiy konfiguratsiyalar. Hardcoded qiymatlar o'rniga
    | bu fayldan foydalaniladi.
    |
    */

    'cache' => [
        'enabled' => env('SITE_CACHE_ENABLED', true),
        'ttl' => [
            'homepage' => env('CACHE_HOMEPAGE_TTL', 3600), // 1 soat
            'page' => env('CACHE_PAGE_TTL', 1800), // 30 daqiqa
            'menu' => env('CACHE_MENU_TTL', 3600), // 1 soat
            'tags' => env('CACHE_TAGS_TTL', 7200), // 2 soat
            'stats' => env('CACHE_STATS_TTL', 3600), // 1 soat
        ],
    ],

    'menus' => [
        'news' => [
            'menu_id' => 1,
            'submenu_id' => 1,
            'multimenu_id' => 1,
        ],
        'announcements' => [
            'menu_id' => 1,
            'submenu_id' => 1,
            'multimenu_id' => 2,
        ],
    ],

    'pagination' => [
        'per_page' => 9,
        'home' => [
            'other_news' => 6,
            'announcements' => 11,
            'announcements_with_activity' => 6,
            'faculties' => 4,
            'departments' => 6,
            'gallery_pages' => 10,
            'gallery_images' => 12,
        ],
    ],

    'meta' => [
        'default_image' => 'img/og-image.webp',
        'description_limit' => 150,
    ],

    'view_tracking' => [
        'enabled' => env('VIEW_TRACKING_ENABLED', true),
        'method' => env('VIEW_TRACKING_METHOD', 'cache'), // 'session', 'cache', 'queue'
        'ttl' => env('VIEW_TRACKING_TTL', 3600), // 1 soat
    ],
];
