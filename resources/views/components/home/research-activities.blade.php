<div class="py-10 lg:py-20 px-4 lg:px-0 bg-white" x-data
    x-intersect.once.threshold.15="$el.classList.add('footer-in')">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-3 text-xl font-medium uppercase md:text-2xl mb-8 text-teal-800 footer-anim footer-anim-d1">
            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-700/10 border border-teal-700/20 shrink-0">
                <img src="/img/icons/013-book-1.webp" alt="Book icon" class="w-5" />
            </span>
            {{ __('messages.sc_activity') }}
        </h1>

        <section id="ilmiy-faoliyat-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if ($announcementsWithActivity->isNotEmpty())
                @foreach ($announcementsWithActivity as $item)
                    <div class="footer-anim"
                         style="transition-delay: {{ number_format(0.1 + $loop->index * 0.12, 2) }}s;">

                        <div class="card-shine group flex flex-col items-center text-center rounded-xl p-6 overflow-hidden
                                    bg-gray-100 border border-gray-200 hover:border-teal-800
                                    hover:-translate-y-1 transition-transform duration-300">

                            <div class="w-40 h-40 rounded-full overflow-hidden mb-4 border-4 border-gray-300 group-hover:border-teal-800/30 transition-colors shrink-0">
                                <x-main.image class="w-full h-full object-cover transition group-hover:scale-[1.04]"
                                    src="{{ $item->imageUrl() }}" alt="{{ lc_title($item) }}" />
                            </div>

                            <h3 class="text-base font-semibold text-gray-800 group-hover:text-teal-800 transition-colors line-clamp-2">
                                <a href="{{ localized_page_route($menuModel ?? $item->menu, $submenuModel ?? $item->submenu, $multimenuModel ?? $item->multimenu, $item) }}">
                                    {{ lc_title($item) }}
                                </a>
                            </h3>

                            <div class="flex mt-4 w-full justify-between items-center text-xs text-gray-400 border-t border-gray-200 pt-3">
                                <span class="flex gap-1.5 items-center">
                                    <img src="/img/icons/011-clock.webp" alt="" class="w-3 opacity-50" />
                                    {{ $item->date?->format('Y-m-d') ?? 'No date' }}
                                </span>
                                <span class="flex gap-1.5 items-center">
                                    <img src="/img/icons/012-user.webp" alt="" class="w-3 opacity-50" />
                                    {{ $item->views ?? 0 }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </section>
    </div>
</div>
