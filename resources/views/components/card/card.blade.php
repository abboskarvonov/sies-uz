@props(['page', 'menuModel', 'submenuModel' => null, 'multimenuModel' => null])

<div class="rounded-lg shadow bg-white dark:bg-gray-800 p-2">
    <a href="{{ localized_page_route($menuModel, $submenuModel, $multimenuModel, $page) }}">
        <x-main.image class="h-60 w-full rounded-t-md object-cover" src="{{ asset('storage/' . $page->image) }}"
            alt="{{ lc_title($page) }}" />
    </a>
    <div class="p-2 pt-5">
        <div class="flex gap-6 text-sm">
            <div class="flex items-center gap-1">
                <img src="/img/icons/011-clock.webp" alt="Book icon" class="w-3 dark:invert" />
                {{ $page->date?->format('Y-m-d') }}
            </div>
            <div class="flex items-center gap-1">
                <img src="/img/icons/012-user.webp" alt="Book icon" class="w-3 dark:invert" />
                {{ $page->views ?? 0 }}
            </div>
        </div>
        <a href="{{ localized_page_route($menuModel, $submenuModel, $multimenuModel, $page) }}">
            <h4 class="my-2 font-bold tracking-tighter text-gray-900 dark:text-white">
                {{ Str::limit(lc_title($page), 44) }}
            </h4>
        </a>
        <p class="text-sm text-gray-700 dark:text-gray-300">
            {{ Str::limit(strip_tags(lc_content($page)), 140) }}
        </p>
        <x-button class="mt-3">
            <a href="{{ localized_page_route($menuModel, $submenuModel, $multimenuModel, $page) }}"
                class="flex items-center gap-2">
                {{ __('messages.read_more') }}
                <svg class="h-3.5 w-3.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 5h12m0 0L9 1m4 4L9 9" />
                </svg>
            </a>
        </x-button>
    </div>
</div>
