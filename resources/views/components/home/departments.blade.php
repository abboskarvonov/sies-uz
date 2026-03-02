<div class="w-full bg-gray-100 py-10 lg:py-20 px-4 lg:px-0"
     x-data x-intersect.once.threshold.10="$el.classList.add('footer-in')">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-3 text-xl font-medium uppercase md:text-2xl text-teal-800 mb-2 footer-anim footer-anim-d1">
            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-700/10 border border-teal-700/20 shrink-0">
                <img src="/img/icons/016-bookmark.webp" alt="Book icon" class="w-5" />
            </span>
            {{ __('messages.departments') }}
        </h1>

        <div class="mt-6 lg:mt-10 grid grid-cols-1 gap-4 pb-10 md:grid-cols-2 lg:grid-cols-3">
            @if ($departments)
                @foreach ($departments as $department)
                    <div class="footer-anim h-full"
                         style="transition-delay: {{ number_format(0.10 + $loop->index * 0.10, 2) }}s;">

                        <a href="{{ localized_page_route($department->menu, $department->submenu, $department->multimenu, $department) }}"
                           class="card-shine group flex items-start gap-4 rounded-xl p-4 h-full overflow-hidden
                                  bg-white
                                  border border-gray-200 border-l-4 border-l-teal-500
                                  hover:border-teal-800
                                  hover:-translate-y-1 transition-transform duration-300">
                            <div class="shrink-0 w-28 h-28 rounded-full overflow-hidden border-2 border-gray-200">
                                <x-main.image class="w-full h-full object-cover transition group-hover:scale-[1.04]"
                                    src="{{ asset('storage/' . $department->image) }}" alt="{{ lc_title($department) }}" />
                            </div>
                            <div class="flex-grow min-w-0">
                                <h3 class="text-base font-bold text-gray-800 group-hover:text-teal-800 transition-colors leading-snug line-clamp-2">
                                    {{ lc_title($department) }}
                                </h3>
                                <p class="text-sm text-gray-500 line-clamp-5 mt-2">
                                    {{ Str::limit(strip_tags(lc_content($department)), 280) }}
                                </p>
                            </div>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>

        @if($departments->isNotEmpty())
            <div class="footer-anim footer-anim-d3 flex justify-center">
                <a href="{{ localized_page_route($departments->first()->menu, $departments->first()->submenu, $departments->first()->multimenu) }}"
                   class="card-shine inline-flex items-center gap-2 px-8 py-3 rounded-xl overflow-hidden
                          bg-teal-800 border border-teal-700/40 hover:border-teal-500/60
                          text-teal-100 hover:text-white text-sm font-semibold
                          hover:-translate-y-0.5 transition-transform duration-300"
                   style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);">
                    {{ __('messages.all_departments') }}
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        @endif
    </div>
</div>
