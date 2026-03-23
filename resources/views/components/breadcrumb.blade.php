@if ($menu)
    <div class="bg-teal-950 px-4 lg:px-0 py-10 border-b border-teal-600">
        <div class="container mx-auto">
            <nav aria-label="Breadcrumb">
                <ol class="inline-flex flex-wrap items-center gap-1.5">

                    {{-- Home --}}
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}"
                            class="header-btn-anim card-shine relative inline-flex items-center justify-center w-9 h-9 rounded-lg overflow-hidden
                                   border border-teal-700/40 hover:border-teal-400/60
                                   bg-teal-800/60 backdrop-blur-md hover:-translate-y-0.5
                                   transition-transform duration-200"
                            style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06); animation-delay: 0.05s"
                            aria-label="Home">
                            <img alt="Home" src="{{ asset('img/icons/home.webp') }}" class="w-4 invert opacity-80">
                        </a>
                    </li>

                    {{-- Chevron --}}
                    <li class="header-btn-anim text-teal-600 select-none" style="animation-delay: 0.10s">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </li>

                    {{-- Menu --}}
                    <li class="inline-flex items-center">
                        <a href="{{ localized_page_route($menu) }}"
                            class="header-btn-anim card-shine relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg overflow-hidden
                                   text-sm font-medium text-teal-100 hover:text-white
                                   border border-teal-700/40 hover:border-teal-400/60
                                   bg-teal-800/60 backdrop-blur-md hover:-translate-y-0.5
                                   transition-transform duration-200"
                            style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06); animation-delay: 0.14s">
                            {{ lc_title($menu) }}
                        </a>
                    </li>

                    {{-- Submenu --}}
                    @if ($submenu)
                        <li class="header-btn-anim text-teal-600 select-none" style="animation-delay: 0.20s">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </li>
                        <li class="inline-flex items-center">
                            <a href="{{ localized_page_route($menu, $submenu) }}"
                                class="header-btn-anim card-shine relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg overflow-hidden
                                       text-sm font-medium text-teal-100 hover:text-white
                                       border border-teal-700/40 hover:border-teal-400/60
                                       bg-teal-800/60 backdrop-blur-md hover:-translate-y-0.5
                                       transition-transform duration-200"
                                style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06); animation-delay: 0.24s">
                                {{ lc_title($submenu) }}
                            </a>
                        </li>
                    @endif

                    {{-- Multimenu --}}
                    @if ($multimenu)
                        <li class="header-btn-anim text-teal-600 select-none" style="animation-delay: 0.30s">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </li>
                        <li class="inline-flex items-center">
                            <a href="{{ localized_page_route($menu, $submenu, $multimenu) }}"
                                class="header-btn-anim card-shine relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg overflow-hidden
                                       text-sm font-medium text-teal-100 hover:text-white
                                       border border-teal-700/40 hover:border-teal-400/60
                                       bg-teal-800/60 backdrop-blur-md hover:-translate-y-0.5
                                       transition-transform duration-200"
                                style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06); animation-delay: 0.34s">
                                {{ lc_title($multimenu) }}
                            </a>
                        </li>
                    @endif

                    {{-- Page --}}
                    @if ($page)
                        <li class="header-btn-anim text-teal-600 select-none" style="animation-delay: 0.40s">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </li>
                        <li aria-current="page" class="inline-flex items-center min-w-0">
                            <a href="{{ localized_page_route($menu, $submenu, $multimenu, $page) }}"
                                class="header-btn-anim card-shine relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg overflow-hidden
                                       text-sm font-medium max-w-40 sm:max-w-60 md:max-w-95
                                       text-white border border-teal-500/50 hover:border-teal-400/70
                                       bg-teal-700/70 backdrop-blur-md hover:-translate-y-0.5
                                       transition-transform duration-200"
                                style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.10); animation-delay: 0.44s">
                                <span class="truncate">{{ lc_title($page) }}</span>
                            </a>
                        </li>
                    @endif

                    {{-- Staff --}}
                    @if ($staff)
                        <li class="header-btn-anim text-teal-600 select-none" style="animation-delay: 0.50s">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </li>
                        <li aria-current="page" class="inline-flex items-center min-w-0">
                            <span
                                class="header-btn-anim relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg overflow-hidden
                                         text-sm font-medium max-w-40 sm:max-w-60 md:max-w-95
                                         text-white border border-teal-500/50
                                         bg-teal-700/70 backdrop-blur-md"
                                style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.10); animation-delay: 0.54s">
                                <span class="truncate">{{ $staff->name ?? lc_name($staff) }}</span>
                            </span>
                        </li>
                    @endif

                </ol>
            </nav>
        </div>
    </div>
@endif
