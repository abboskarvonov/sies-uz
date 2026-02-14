<section class="container mx-auto px-4 lg:px-0 py-10 lg:py-14">
    {{-- Title row --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <img src="{{ asset('img/icons/010-ssd.webp') }}" alt="Icon" class="w-6 h-6 dark:invert" />
            <h2 class="text-xl md:text-2xl font-semibold tracking-tight">{{ __('messages.news') }}</h2>
        </div>
        @if($latestNews)
            <a href="{{ localized_page_route($latestNews->menu, $latestNews->submenu, $latestNews->multimenu) }}"
                class="text-sm font-medium">{{ __('messages.all') }}</a>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- LEFT: Featured + grid --}}
        <div class="lg:col-span-2 grid gap-6">
            {{-- Featured card --}}
            @if ($latestNews)
                <a href="{{ localized_page_route($latestNews->menu, $latestNews->submenu, $latestNews->multimenu, $latestNews) }}"
                    class="group relative block overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-800">
                    <x-main.image src="{{ 'storage/' . $latestNews->image }}"
                        class="h-[350px] md:h-[450px] xl:h-[600px] w-full object-cover transition group-hover:scale-[1.02]"
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
                        <h3 class="text-lg md:text-2xl font-semibold leading-tight line-clamp-1">
                            {{ Str::limit(lc_title($latestNews), 90) }}
                        </h3>
                    </div>
                </a>
            @endif

            {{-- 4-card grid --}}
            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                @foreach (($otherNews ?? collect())->take(6) as $item)
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
                            <h4 class="line-clamp-2 text-sm font-semibold">
                                {{ Str::limit(lc_title($item), 90) }}
                            </h4>
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
                @if($announcements->isNotEmpty())
                    <a href="{{ localized_page_route($announcements->first()->menu, $announcements->first()->submenu, $announcements->first()->multimenu) }}"
                        class="text-xs">{{ __('messages.all') }}</a>
                @endif
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
