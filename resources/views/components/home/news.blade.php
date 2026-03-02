<section class="bg-white px-4 lg:px-0 py-10 lg:py-14" x-data
    x-intersect.once.threshold.15="$el.classList.add('footer-in')">
    <div class="container mx-auto">

        {{-- Title row --}}
        <div class="mb-6 flex items-center justify-between footer-anim footer-anim-d1">
            <h1
                class="flex items-center gap-3 text-xl font-medium uppercase md:text-2xl text-teal-800 footer-anim footer-anim-d1">
                <span
                    class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-700/10 border border-teal-700/20 shrink-0">
                    <img src="{{ asset('img/icons/010-ssd.webp') }}" alt="Icon" class="w-5 h-5" />
                </span>
                {{ __('messages.news') }}
            </h1>
            @if ($latestNews)
                <a href="{{ localized_page_route($latestNews->menu, $latestNews->submenu, $latestNews->multimenu) }}"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-teal-700 hover:text-teal-800 transition-colors">
                    {{ __('messages.all') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            @endif
        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- LEFT: Featured + grid --}}
            <div class="lg:col-span-2 grid gap-6">

                {{-- Featured card --}}
                @if ($latestNews)
                    <a href="{{ localized_page_route($latestNews->menu, $latestNews->submenu, $latestNews->multimenu, $latestNews) }}"
                        class="card-shine footer-anim group relative block overflow-hidden rounded-2xl bg-gray-200"
                        style="transition-delay: 0.15s">
                        <x-main.image src="{{ 'storage/' . $latestNews->image }}"
                            class="h-87.5 md:h-112.5 xl:h-150 w-full object-cover transition group-hover:scale-[1.02]"
                            alt="{{ lc_title($latestNews) }}" />
                        <div class="pointer-events-none absolute inset-0 bg-linear-to-t from-black/70 to-transparent">
                        </div>

                        <div class="absolute bottom-0 w-full p-5 md:p-6 text-white">
                            <div class="mb-2 flex items-center gap-2 text-xs">
                                <span
                                    class="inline-flex items-center rounded-full bg-teal-500/25 px-2 py-1 backdrop-blur text-teal-100">
                                    {{ __('messages.news') }}
                                </span>
                                <span class="opacity-80 flex gap-1 items-center">
                                    <img src="/img/icons/011-clock.webp" alt="" class="w-3 h-3 invert" />
                                    {{ optional($latestNews->date)->format('Y-m-d') }}
                                </span>
                                <span class="opacity-80 flex gap-1 items-center">
                                    <img src="/img/icons/012-user.webp" alt="" class="w-3 h-3 invert" />
                                    {{ $latestNews->views ?? 0 }}
                                </span>
                            </div>
                            <h3 class="text-lg md:text-2xl font-semibold leading-tight line-clamp-1">
                                {{ Str::limit(lc_title($latestNews), 90) }}
                            </h3>
                        </div>
                    </a>
                @endif

                {{-- Small cards grid --}}
                <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                    @foreach (($otherNews ?? collect())->take(6) as $item)
                        <div class="footer-anim" style="transition-delay: {{ 0.25 + $loop->index * 0.1 }}s">
                            <a href="{{ localized_page_route($menuModel ?? $item->menu, $submenuModel ?? $item->submenu, $multimenuModel ?? $item->multimenu, $item) }}"
                                class="card-shine group overflow-hidden rounded-xl flex flex-col
                                       bg-white border border-gray-200 hover:border-teal-800
                                       shadow-sm hover:shadow-md hover:-translate-y-1 transition-transform duration-300">
                                <div class="relative">
                                    <x-main.image src="{{ 'storage/' . $item->image }}"
                                        class="h-40 w-full object-cover transition group-hover:scale-[1.02]"
                                        alt="{{ lc_title($item) }}" />
                                    <span
                                        class="absolute left-2 top-2 rounded-full bg-black/60 px-2 py-0.5
                                                 text-[11px] font-medium text-white flex items-center gap-3">
                                        <span class="flex gap-1 items-center">
                                            <img src="/img/icons/011-clock.webp" alt=""
                                                class="inline w-3 h-3 invert" />
                                            {{ optional($item->date)->format('Y-m-d') }}
                                        </span>
                                        <span class="flex gap-1 items-center">
                                            <img src="/img/icons/012-user.webp" alt=""
                                                class="inline w-3 h-3 invert" />
                                            {{ $item->views ?? 0 }}
                                        </span>
                                    </span>
                                </div>
                                <div class="p-3">
                                    <h4
                                        class="line-clamp-2 text-sm font-semibold text-gray-800 group-hover:text-teal-800 transition-colors">
                                        {{ Str::limit(lc_title($item), 90) }}
                                    </h4>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- RIGHT: Announcements panel --}}
            <aside
                class="rounded-2xl bg-white border border-gray-200
                          shadow-sm p-4 footer-anim"
                style="transition-delay: 0.20s">
                <div class="mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('img/icons/ann.webp') }}" alt="" class="w-6 h-6" />
                        <h3 class="font-semibold text-gray-800">{{ __('messages.ad') }}</h3>
                    </div>
                    @if ($announcements->isNotEmpty())
                        <a href="{{ localized_page_route($announcements->first()->menu, $announcements->first()->submenu, $announcements->first()->multimenu) }}"
                            class="text-xs text-teal-600 hover:text-teal-800 transition-colors">{{ __('messages.all') }}</a>
                    @endif
                </div>

                <div class="space-y-2">
                    @forelse($announcements as $ad)
                        <div class="footer-anim" style="transition-delay: {{ 0.3 + $loop->index * 0.08 }}s">
                            <a href="{{ localized_page_route($menuModel ?? $ad->menu, $submenuModel ?? $ad->submenu, $multimenuModel ?? $ad->multimenu, $ad) }}"
                                class="card-shine group grid grid-cols-[64px_1fr] gap-3 rounded-lg p-2 overflow-hidden
                                       bg-gray-50 border border-gray-100 hover:border-teal-800
                                       hover:-translate-y-0.5 transition-transform duration-300">
                                <x-main.image src="{{ 'storage/' . $ad->image }}"
                                    class="h-16 w-16 rounded-md object-cover" alt="{{ lc_title($ad) }}" />
                                <div>
                                    <h4
                                        class="line-clamp-2 text-sm font-medium text-gray-700 group-hover:text-teal-800 transition-colors">
                                        {{ Str::limit(lc_title($ad), 70) }}
                                    </h4>
                                    <div class="mt-1 flex items-center gap-3 text-[11px] text-gray-400">
                                        <span class="flex gap-1 items-center">
                                            <img src="{{ asset('/img/icons/011-clock.webp') }}" alt=""
                                                class="w-3 opacity-50" />
                                            {{ optional($ad->date)->format('Y-m-d') }}
                                        </span>
                                        <span class="flex gap-1 items-center">
                                            <img src="/img/icons/012-user.webp" alt="" class="w-3 opacity-50" />
                                            {{ $ad->views ?? 0 }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="p-4 text-center text-sm text-gray-400">
                            {{ __('E\'lonlar hozircha yo\'q') }}
                        </div>
                    @endforelse
                </div>
            </aside>

        </div>
    </div>
</section>
