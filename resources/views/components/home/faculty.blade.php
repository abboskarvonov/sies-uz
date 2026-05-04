<div class="w-full bg-gray-100 py-10 lg:py-20 px-4 lg:px-0" x-data
    x-intersect.once.threshold.10="$el.classList.add('footer-in')">

    <div class="container mx-auto">

        {{-- Header row --}}
        <div class="flex items-end justify-between mb-8 footer-anim footer-anim-d1">
            <h1 class="flex items-center gap-3 text-xl font-medium uppercase md:text-2xl text-teal-800">
                <span
                    class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-700/10 border border-teal-700/20 shrink-0">
                    <img src="/img/icons/014-graduation-hat.webp" alt="Book icon" class="w-5" />
                </span>
                {{ __('messages.faculty') }}
            </h1>
            @if ($faculties->isNotEmpty())
                <a href="{{ localized_page_route($faculties->first()->menu, $faculties->first()->submenu, $faculties->first()->multimenu) }}"
                    class="hidden md:inline-flex items-center gap-1.5 text-sm text-teal-700 hover:text-teal-900 transition-colors">
                    {{ __('messages.all_faculties') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            @endif
        </div>

        {{-- Cards grid --}}
        <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-5">
            @if ($faculties)
                @foreach ($faculties as $faculty)
                    {{-- Outer: entrance animation --}}
                    <div class="footer-anim"
                        style="transition-delay: {{ number_format(0.08 + $loop->index * 0.1, 2) }}s;">

                        {{-- Poster card --}}
                        <a href="{{ localized_page_route($faculty->menu, $faculty->submenu, $faculty->multimenu, $faculty) }}"
                            class="card-shine group relative block aspect-2/3 rounded-2xl overflow-hidden
                                  border border-teal-700/40 hover:border-teal-400/60
                                  hover:-translate-y-2 transition-transform duration-300"
                            style="box-shadow: 0 8px 32px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.06);">

                            {{-- Full-bleed image --}}
                            <x-main.image
                                class="absolute inset-0 w-full h-full object-cover
                                       transition-transform duration-700 group-hover:scale-110 will-change-transform"
                                src="{{ $faculty->imageUrl() }}" alt="{{ lc_title($faculty) }}" />

                            {{-- Gradient overlay: resting state --}}
                            <div
                                class="absolute inset-0 bg-linear-to-t
                                        from-teal-950 via-teal-950/50 to-teal-900/10
                                        group-hover:via-teal-950/30 group-hover:to-transparent
                                        transition-all duration-500">
                            </div>

                            {{-- Number badge --}}
                            <div
                                class="absolute top-3 left-3
                                        w-8 h-8 rounded-lg
                                        bg-black/30 backdrop-blur-sm border border-white/20
                                        flex items-center justify-center">
                                <span class="text-xs font-bold text-white/80">
                                    {{ str_pad($loop->index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>

                            {{-- Teal accent dot (top-right) --}}
                            <div
                                class="absolute top-3 right-3 w-2 h-2 rounded-full bg-teal-400
                                        opacity-0 group-hover:opacity-100 transition-opacity duration-300
                                        shadow-[0_0_8px_rgba(45,212,191,0.8)]">
                            </div>

                            {{-- Bottom text block --}}
                            <div
                                class="absolute bottom-0 left-0 right-0 p-4
                                        translate-y-1 group-hover:translate-y-0
                                        transition-transform duration-400">
                                <h3 class="text-sm md:text-base font-bold text-white leading-snug line-clamp-2 mb-2">
                                    {{ lc_title($faculty) }}
                                </h3>
                                {{-- "Batafsil" row — only visible on hover --}}
                                <div
                                    class="flex items-center gap-1.5
                                            opacity-0 group-hover:opacity-100
                                            -translate-y-1 group-hover:translate-y-0
                                            transition-all duration-300">
                                    <span class="text-xs text-teal-300 font-medium">{{ __('messages.more') }}</span>
                                    <svg class="w-3 h-3 text-teal-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            @endif
        </section>

        {{-- Mobile "all" button --}}
        @if ($faculties->isNotEmpty())
            <div class="footer-anim footer-anim-d3 flex justify-center mt-8 md:hidden">
                <a href="{{ localized_page_route($faculties->first()->menu, $faculties->first()->submenu, $faculties->first()->multimenu) }}"
                    class="card-shine inline-flex items-center gap-2 px-8 py-3 rounded-xl overflow-hidden
                          bg-teal-800 border border-teal-700/40 hover:border-teal-500/60
                          text-teal-100 hover:text-white text-sm font-semibold
                          hover:-translate-y-0.5 transition-transform duration-300"
                    style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);">
                    {{ __('messages.all_faculties') }}
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        @endif
    </div>
</div>
