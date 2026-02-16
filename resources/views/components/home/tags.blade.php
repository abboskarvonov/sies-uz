<?php
// Ranglar va SVG Ikonkalari Ro'yxati (Font Awesome o'rniga inline SVG)
$design_items = [
    // 1. Yangiliklar (Newspaper)
    [
        'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>',
    ],
    // 2. Talabalar (User-Graduate)
    [
        'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>',
    ],
    // 3. Ta'lim/Kurslar (Book-Open)
    [
        'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
    ],
    // 4. Ilmiy Faoliyat (Flask)
    [
        'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>',
    ],
    // 5. E'lonlar (Bullhorn/Megaphone)
    [
        'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>',
    ],
    // 6. Kafedra/Bino (Building)
    [
        'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
    ],
];
$item_count = count($design_items);
$index = 0; // Sikl indexini boshlash
?>

<div class="w-full lg:py-20 px-4 lg:px-0">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl">
            <img src="/img/icons/hastag.webp" alt="Hash icon" class="w-6 dark:invert" />
            {{ __('messages.tags') }}
        </h1>

        <div class="mt-6 flex flex-wrap gap-4">
            @if ($tags)
                @foreach ($tags as $tag)
                    <?php
                    // Rang va Ikonka klassini $design_items arrayidan navbatma-navbat olish
                    $design = $design_items[$index % $item_count];
                    $index++;
                    ?>

                    <a href="{{ route('tags.show', ['slug' => $tag->slug]) }}"
                        class="flex items-center bg-gray-50 rounded-xl shadow py-3 px-6 group transition-all duration-300
                                border-r-4 border-gray-600 hover:shadow-lg dark:hover:shadow-gray-800 hover:bg-gray-200
                                dark:bg-gray-700 dark:border-gray-500 dark:hover:bg-gray-600">

                        <div
                            class="w-14 h-14 flex items-center justify-center bg-gray-100 rounded-lg mr-4 transition-colors dark:bg-gray-900/50 text-gray-600 dark:text-gray-400">
                            {!! $design['icon'] !!}
                        </div>

                        <span
                            class="text-xl font-bold text-gray-800 group-hover:text-gray-700 dark:text-white dark:group-hover:text-gray-300">
                            {{ $tag->name }}
                        </span>
                    </a>
                @endforeach
            @endif
        </div>
    </div>
</div>
