{{-- <div class="py-10 lg:py-20 px-4">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl">
            <img src="{{ asset('img/icons/010-ssd.webp') }}" alt="Book icon" class="w-6 dark:invert" />
            News
        </h1>
        <div class="mt-5 flex flex-wrap justify-between md:mt-10">
            @php
                $locale = app()->getLocale();
                $titleField = 'title_' . $locale;
                $slugField = 'slug_' . $locale;
            @endphp
            @if ($latestNews)
                <div class="relative h-[550px] w-full overflow-hidden rounded-xl lg:w-2/5">
                    <div class="news-overlay z-10"></div>
                    <x-main.image class="h-full w-full object-cover" :lazy="false"
                        src="{{ asset('storage/' . $latestNews->image) }}" width="600" height="400" />
                    <div class="absolute bottom-14 left-0 z-20 grid w-full px-10 py-5 text-white">
                        <a href="{{ localized_page_route($latestNews->menu, $latestNews->submenu, $latestNews->multimenu, $latestNews) }}"
                            class="text-lg">
                            {{ $latestNews->$titleField }}
                        </a>
                        <div class="mt-2 flex gap-10 text-sm">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('/img/icons/011-clock.webp') }}" alt="Book icon"
                                    class="w-3 invert" />
                                {{ $latestNews->date?->format('Y-m-d') ?? 'No date' }}
                            </div>
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('/img/icons/012-user.webp') }}" alt="Book icon" class="w-3 invert" />
                                {{ $latestNews->views ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-5 hidden h-[550px] content-between md:grid md:w-2/4 lg:mt-0 lg:w-1/4">
                @foreach ($otherNews as $news)
                    <div class="relative h-[172px] w-full overflow-hidden rounded-xl">
                        <x-main.image class="h-full w-full object-cover" src="{{ asset('storage/' . $news->image) }}" />
                        <div class="absolute bottom-2 z-20 w-full p-4 text-sm text-white">
                            <a href="{{ localized_page_route($news->menu, $news->submenu, $news->multimenu, $news) }}">
                                {{ Str::limit($news->{'title_' . app()->getLocale()}, 100) }}
                            </a>
                        </div>
                        <div class="news-small-overlay"></div>
                    </div>
                @endforeach

            </div>

            <div
                class="mt-5 grid h-[550px] w-full content-between overflow-hidden rounded-xl bg-gray-200 p-4 shadow-inner dark:bg-gray-800 md:w-5/12 lg:mt-0 lg:w-1/3">
                <div class="flex items-center gap-4 border-b-2 border-b-foreground pb-3">
                    <img src="{{ asset('img/icons/ann.webp') }}" alt="SamISI" class="w-8 dark:invert" />
                    <p class="text-2xl font-bold">
                        Advertisements
                    </p>
                </div>
                @foreach ($announcements as $item)
                    <div
                        class="h-[140px] overflow-hidden rounded-lg border border-solid border-gray-400 bg-gray-50 shadow-lg dark:border-gray-400 dark:bg-gray-500">
                        <div class="flex justify-between">
                            <div class="w-3/5 ps-3 pt-3 text-sm">
                                <a
                                    href="{{ localized_page_route($item->menu, $item->submenu, $item->multimenu, $item) }}">
                                    {{ Str::limit($item->{'title_' . app()->getLocale()}, 100) }}
                                </a>
                                <div class="flex gap-8 pt-2">
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
                            <div class="relative h-[140px] w-2/5">
                                <img class="h-full w-full object-cover" src="{{ asset('storage/' . $item->image) }}" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div> --}}
<section class="container mx-auto px-4 lg:px-0 py-10 lg:py-14">
    {{-- Title row --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <img src="{{ asset('img/icons/010-ssd.webp') }}" alt="Icon" class="w-6 h-6 dark:invert" />
            <h2 class="text-xl md:text-2xl font-semibold tracking-tight">{{ __('messages.news') }}</h2>
        </div>
        <a href="{{ localized_page_route($latestNews->menu, $latestNews->submenu, $latestNews->multimenu) }}"
            class="text-sm font-medium">{{ __('messages.all') }}</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- LEFT: Featured + grid --}}
        <div class="lg:col-span-2 grid gap-6">
            {{-- Featured card --}}
            @if ($latestNews)
                <a href="{{ localized_page_route($latestNews->menu, $latestNews->submenu, $latestNews->multimenu, $latestNews) }}"
                    class="group relative block overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-800">
                    <x-main.image src="{{ 'storage/' . $latestNews->image }}"
                        class="h-[320px] w-full object-cover transition group-hover:scale-[1.02]"
                        alt="{{ lc_title($latestNews) }}" />
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/60 to-transparent">
                    </div>

                    <div class="absolute bottom-0 w-full p-5 md:p-6 text-white">
                        <div class="mb-2 flex items-center gap-2 text-xs">
                            <span class="inline-flex items-center rounded-full bg-white/15 px-2 py-1 backdrop-blur">
                                {{ __('messages.news') }}
                            </span>
                            <span class="opacity-80 flex gap-1 items-center"><img src="/img/icons/011-clock.webp"
                                    alt="Book icon" class="w-3 h-3 invert dark:invert-0" />
                                {{ optional($latestNews->date)->format('Y-m-d') }}</span>
                            <span class="opacity-80 flex gap-1 items-center"><img src="/img/icons/012-user.webp"
                                    alt="Book icon" class="w-3 h-3 invert dark:invert-0" />
                                {{ $latestNews->views ?? 0 }}</span>
                        </div>
                        <h3 class="text-lg md:text-2xl font-semibold leading-tight">
                            {{ Str::limit(lc_title($latestNews), 90) }}
                        </h3>
                    </div>
                </a>
            @endif

            {{-- 4-card grid --}}
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach (($otherNews ?? collect())->take(4) as $item)
                    <a href="{{ localized_page_route($menuModel ?? $item->menu, $submenuModel ?? $item->submenu, $multimenuModel ?? $item->multimenu, $item) }}"
                        class="group overflow-hidden rounded-xl bg-white shadow hover:shadow-lg transition dark:bg-gray-800">
                        <div class="relative">
                            <x-main.image src="{{ 'storage/' . $item->image }}"
                                class="h-40 w-full object-cover transition group-hover:scale-[1.02]"
                                alt="{{ lc_title($item) }}" />
                            <span
                                class="absolute left-2 top-2 rounded-full bg-white/90 px-2 py-0.5 text-[11px] font-medium text-gray-900 dark:bg-black/60 dark:text-white flex items-center gap-4">
                                <span class="flex gap-1 items-center">
                                    <img src="/img/icons/011-clock.webp" alt="Book icon"
                                        class="inline w-3 h-3 dark:invert-0" />
                                    {{ optional($item->date)->format('Y-m-d') }}
                                </span>
                                <span class="flex gap-1 items-center"><img src="/img/icons/012-user.webp"
                                        alt="Book icon"
                                        class="inline w-3 h-3 dark:invert-0" />{{ $item->views ?? 0 }}</span>
                            </span>
                        </div>
                        <div class="p-3">
                            <h4 class="line-clamp-2 font-semibold">
                                {{ Str::limit(lc_title($item), 80) }}
                            </h4>
                            <p class="mt-1 line-clamp-2 text-sm text-gray-600 dark:text-gray-300">
                                {{ Str::limit(strip_tags(lc_content($item)), 90) }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- RIGHT: Announcements panel --}}
        <aside class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('img/icons/ann.webp') }}" alt="SamISI" class="w-6 h-6 dark:invert" />
                    <h3 class="font-semibold">{{ __('messages.ad') }}</h3>
                </div>
                <a href="{{ localized_page_route($announcements[0]->menu, $announcements[0]->submenu, $announcements[0]->multimenu) }}"
                    class="text-xs">{{ __('messages.all') }}</a>
            </div>

            <div class="space-y-3">
                @forelse($announcements as $ad)
                    <a href="{{ localized_page_route($menuModel ?? $ad->menu, $submenuModel ?? $ad->submenu, $multimenuModel ?? $ad->multimenu, $ad) }}"
                        class="group grid grid-cols-[64px,1fr] gap-3 rounded-lg p-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <x-main.image src="{{ 'storage/' . $ad->image }}" class="h-16 w-16 rounded-md object-cover"
                            alt="{{ lc_title($ad) }}" />
                        <div>
                            <h4
                                class="line-clamp-2 text-sm font-medium group-hover:text-gray-600 dark:group-hover:text-gray-400">
                                {{ Str::limit(lc_title($ad), 70) }}
                            </h4>
                            <div class="mt-1 flex items-center gap-3 text-[11px] text-gray-500">
                                <span class="flex gap-2 items-center"><img
                                        src="{{ asset('/img/icons/011-clock.webp') }}" alt="Book icon"
                                        class="w-3 dark:invert" />{{ optional($ad->date)->format('Y-m-d') }}</span>
                                <span class="flex gap-2 items-center"><img src="/img/icons/012-user.webp"
                                        alt="Book icon" class="w-3 dark:invert" />{{ $ad->views ?? 0 }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-300">
                        {{ __('E’lonlar hozircha yo‘q') }}
                    </div>
                @endforelse
            </div>
        </aside>
    </div>
</section>
