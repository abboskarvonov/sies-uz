<?php
// Ranglar va Font Awesome Ikonkalari Ro'yxati
$design_items = [
    // 1. Blue (Ko'k) - Yangiliklar (Fa-Newspaper)
    [
        'icon_class' => 'fa-newspaper',
    ],
    // 2. Green (Yashil) - Talabalar/O'quvchilar (Fa-User-Graduate)
    [
        'icon_class' => 'fa-user-graduate',
    ],
    // 3. Orange (Apelsin) - Ta'lim/Kurslar (Fa-Book-Open)
    [
        'icon_class' => 'fa-book-open',
    ],
    // 4. Purple (Binafsha) - Ilmiy Faoliyat/Tadqiqot (Fa-Flask)
    [
        'icon_class' => 'fa-flask',
    ],
    // 5. Red (Qizil) - E'lonlar/Xabarlar (Fa-Bullhorn)
    [
        'icon_class' => 'fa-bullhorn',
    ],
    // 6. Teal (Moviy-yashil) - Kafedra/Bino (Fa-Building-Columns)
    [
        'icon_class' => 'fa-building-columns',
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
                    $icon_class = $design['icon_class'];
                    $index++;
                    ?>

                    <a href="{{ route('tags.show', ['slug' => $tag->slug]) }}"
                        class="flex items-center bg-gray-50 rounded-xl shadow py-3 px-6 group transition-all duration-300
                                border-r-4 border-gray-600 hover:shadow-lg dark:hover:shadow-gray-800 hover:bg-gray-200 
                                dark:bg-gray-700 dark:border-gray-500 dark:hover:bg-gray-600">

                        <div
                            class="w-14 h-14 flex items-center justify-center bg-gray-100 rounded-lg mr-4 transition-colors dark:bg-gray-900/50">
                            <i
                                class="fa-solid {{ $icon_class }} text-gray-600 w-7 h-7 text-xl group-hover:text-gray-600 dark:text-gray-400"></i>
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
