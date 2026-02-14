@if ($menu)
    <div class="bg-gray-100 dark:bg-gray-950 px-4 lg:px-0">
        <div class="container mx-auto py-10">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex flex-wrap items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    {{-- Home --}}
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}"
                            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                            <img alt="Home" src="{{ asset('img/icons/home.webp') }}" class="w-4 dark:invert">
                        </a>
                    </li>

                    {{-- Menu --}}
                    <li>
                        <div class="flex items-center">
                            <span class="text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg> {{-- SVG chevron --}}
                            </span>
                            <a href="{{ localized_page_route($menu) }}"
                                class="ms-1 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white md:ms-2">
                                {{ lc_title($menu) }}
                            </a>
                        </div>
                    </li>

                    {{-- Submenu --}}
                    @if ($submenu)
                        <li>
                            <div class="flex items-center">
                                <span class="text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                                <a href="{{ localized_page_route($menu, $submenu) }}"
                                    class="ms-1 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white md:ms-2">
                                    {{ lc_title($submenu) }}
                                </a>
                            </div>
                        </li>
                    @endif

                    {{-- Multimenu --}}
                    @if ($multimenu)
                        <li>
                            <div class="flex items-center">
                                <span class="text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                                <a href="{{ localized_page_route($menu, $submenu, $multimenu) }}"
                                    class="ms-1 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white md:ms-2">
                                    {{ lc_title($multimenu) }}
                                </a>
                            </div>
                        </li>
                    @endif

                    {{-- Page --}}
                    @if ($page)
                        <li aria-current="page">
                            <div class="flex items-center">
                                <span class="text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                                <a href="{{ localized_page_route($menu, $submenu, $multimenu, $page) }}"
                                    class="ms-1 text-sm truncate max-w-[180px] sm:max-w-[260px] md:max-w-[400px] font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white md:ms-2">
                                    {{ lc_title($page) }}
                                </a>
                            </div>
                        </li>
                    @endif

                    {{-- Page --}}
                    @if ($staff)
                        <li aria-current="page">
                            <div class="flex items-center">
                                <span class="text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                                <span
                                    class="ms-1 text-sm truncate max-w-[180px] sm:max-w-[260px] md:max-w-[360px] font-medium text-gray-900 dark:text-gray-200 md:ms-2">
                                    {{ lc_name($staff) }}
                                </span>
                            </div>
                        </li>
                    @endif
                </ol>
            </nav>
        </div>
    </div>
@endif
