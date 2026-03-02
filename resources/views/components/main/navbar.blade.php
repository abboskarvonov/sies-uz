<div class="sticky top-0 z-40 py-4 px-1 border-b border-teal-500/30 bg-gray-100 nav-glass"
    style="box-shadow: inset 0 -1px 0 rgba(5, 77, 14, 0.15), 0 4px 20px rgba(15, 50, 1, 0.743);">

    <div class="container mx-auto flex flex-wrap items-center justify-between">
        @include('components.main.nav-logo')

        {{-- ═══ DESKTOP MENU ═══ --}}
        <div class="hidden gap-2 lg:flex">
            <div x-data="{ openMenu: null, openSubmenu: null }" class="relative flex text-left">
                <div class="flex space-x-1">
                    @foreach ($menus as $menu)
                        <div class="relative header-btn-anim" style="animation-delay: {{ 200 + $loop->index * 70 }}ms"
                            @mouseenter="openMenu = {{ $menu->id }}, openSubmenu = null"
                            @mouseleave="openMenu = null, openSubmenu = null">

                            {{-- 1-daraja: Asosiy menu tugmasi --}}
                            <x-button class="capitalize">
                                {{ $menu->{'title_' . app()->getLocale()} }}
                            </x-button>

                            {{-- 2-daraja: Submenu panel --}}
                            {{-- overflow-hidden YOʻQ — 3-darajali menuni qirqib tashlaydi --}}
                            <div x-show="openMenu === {{ $menu->id }}" x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-95"
                                class="nav-dropdown absolute top-full left-0 mt-2 w-52 z-50 rounded-xl py-1
                                        bg-gray-100 dark:bg-gray-900/95
                                        backdrop-blur-xl
                                        border border-teal-700 dark:border-white/8
                                        shadow-xl shadow-black/10 dark:shadow-black/40">

                                @foreach ($menu->submenus as $submenu)
                                    <div class="relative" @mouseenter="openSubmenu = {{ $submenu->id }}"
                                        @mouseleave="openSubmenu = null">

                                        {{-- Submenu tugmasi --}}
                                        <button
                                            class="flex items-center justify-between w-full px-3 py-2 mx-1 text-sm text-left
                                                       text-gray-700 dark:text-gray-200
                                                       rounded-lg hover:bg-gray-100 dark:hover:bg-white/[0.07]
                                                       transition-colors duration-150"
                                            style="width: calc(100% - 8px);">
                                            <span>{{ $submenu->{'title_' . app()->getLocale()} }}</span>
                                            <svg class="w-3.5 h-3.5 shrink-0 ml-2 opacity-50" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>

                                        {{-- 3-daraja: Multimenu panel --}}
                                        <div x-show="openSubmenu === {{ $submenu->id }}" x-cloak
                                            x-transition:enter="transition ease-out duration-150"
                                            x-transition:enter-start="opacity-0 -translate-x-1 scale-95"
                                            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                                            x-transition:leave="transition ease-in duration-100"
                                            x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="nav-dropdown absolute left-full top-0 ml-2 w-60 z-50 rounded-xl py-1
                                                    max-h-[70vh] overflow-y-auto
                                                    bg-gray-100 dark:bg-gray-900/95
                                                    backdrop-blur-xl
                                                    border border-teal-700 dark:border-white/8
                                                    shadow-xl shadow-black/10 dark:shadow-black/40">

                                            @foreach ($submenu->multimenus as $multimenu)
                                                <a href="{{ $multimenu->link ?: localized_page_route($menu, $submenu, $multimenu) }}"
                                                    target="{{ $multimenu->link ? '_blank' : '_self' }}"
                                                    class="block px-3 py-2 mx-1 text-sm
                                                           dark:text-gray-200
                                                          rounded-lg hover:bg-gray-200 hover:text-gray-800 dark:hover:bg-white/[0.07]
                                                          transition-colors duration-150"
                                                    style="width: calc(100% - 8px);">
                                                    {{ $multimenu->{'title_' . app()->getLocale()} }}
                                                </a>
                                            @endforeach
                                        </div>

                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @include('components.main.mobile-menu')
    </div>
</div>
