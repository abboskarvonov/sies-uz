<div class="container mx-auto py-10 lg:py-20 px-4 lg:px-0">
    <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl mb-5">
        <img src="/img/icons/013-book-1.webp" alt="Book icon" class="w-6 dark:invert" />
        {{ __('messages.sc_activity') }}
    </h1>

    <section id="ilmiy-faoliyat-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @if ($announcementsWithActivity->isNotEmpty())
            @foreach ($announcementsWithActivity as $item)
                <div
                    class="bg-white dark:bg-gray-700 rounded-xl shadow-xl p-6 border-t-4 border-gray-500 dark:border-gray-400 transition-shadow duration-300 hover:shadow-2xl">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="w-44 h-44 rounded-full overflow-hidden mb-4 border-4 border-gray-200 dark:border-gray-400">
                            <x-main.image class="w-full h-full object-cover"
                                src="{{ asset('storage/' . $item->image) }}" alt="{{ lc_title($item) }}" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 line-clamp-2">
                            <a
                                href="{{ localized_page_route($menuModel ?? $item->menu, $submenuModel ?? $item->submenu, $multimenuModel ?? $item->multimenu, $item) }}">{{ lc_title($item) }}</a>
                        </h3>
                    </div>

                    <div
                        class="flex mt-5 justify-between items-center text-xs text-gray-500 dark:text-gray-300 border-t pt-3">
                        <span class="flex gap-2 items-center">
                            <img src="/img/icons/011-clock.webp" alt="Book icon" class="w-3 dark:invert" />
                            {{ $item->date?->format('Y-m-d') ?? 'No date' }}
                        </span>
                        <span class="flex gap-2 items-center">
                            <img src="/img/icons/012-user.webp" alt="Book icon" class="w-3 dark:invert" />
                            {{ $item->views ?? 0 }}
                        </span>
                    </div>
                </div>
            @endforeach
        @endif
    </section>
</div>
{{-- <div class="mt-5 grid grid-cols-1 justify-between gap-3 md:mt-10 md:grid-cols-2 md:gap-8">
        
                <div
                    class="flex items-center justify-between overflow-hidden rounded-xl bg-gray-100 p-1 dark:bg-gray-700 md:p-4">
                   
                    <div class="relative h-52 w-2/5 overflow-hidden rounded-lg shadow-md">
                        <x-main.image src="{{ asset('storage/' . $item->image) }}" alt="{{ lc_title($item) }}"
                            class="h-full w-full object-cover" />
                    </div>

                    
                    <div class="w-3/5 px-4">
                        <a href="{{ localized_page_route($menuModel ?? $item->menu, $submenuModel ?? $item->submenu, $multimenuModel ?? $item->multimenu, $item) }}"
                            class="text-md">
                            {{ lc_title($item) }}
                        </a>
                        <div class="lg:text-md mt-4 flex items-center gap-5 text-sm lg:gap-10">
                            <div class="flex items-center gap-2">
                                <img src="/img/icons/011-clock.webp" alt="Book icon" class="w-3 dark:invert" />
                                {{ $item->date?->format('Y-m-d') ?? 'No date' }}
                            </div>
                            <div class="flex items-center gap-2">
                                <img src="/img/icons/012-user.webp" alt="Book icon" class="w-3 dark:invert" />
                                {{ $item->views ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            
    </div>
</div> --}}
