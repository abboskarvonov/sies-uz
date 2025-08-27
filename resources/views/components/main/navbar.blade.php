<div class="sticky top-0 z-40 border-b border-b-gray-600 bg-white py-2 px-1 dark:bg-gray-700">
    <div class="container mx-auto flex flex-wrap items-center justify-between">
        @include('components.main.nav-logo')
        <div class="hidden gap-2 lg:flex">
            <!-- Parent x-data -->
            <div x-data="{ openMenu: null, openSubmenu: null }" class="relative flex text-left">

                <!-- Main Menus -->
                <div class="flex space-x-2">
                    @foreach ($menus as $menu)
                        <div class="relative" @mouseenter="openMenu = {{ $menu->id }}, openSubmenu = null"
                            @mouseleave="openMenu = null, openSubmenu = null">
                            <!-- Main Menu button -->
                            <x-button class="capitalize">
                                {{ $menu->{'title_' . app()->getLocale()} }}
                            </x-button>

                            <!-- Submenu -->
                            <div x-show="openMenu === {{ $menu->id }}" x-cloak x-transition
                                class="absolute mt-2 w-48 bg-white dark:bg-gray-900 border rounded shadow-lg z-50 dark:border-gray-800">
                                @foreach ($menu->submenus as $submenu)
                                    <div class="relative" @mouseenter="openSubmenu = {{ $submenu->id }}"
                                        @mouseleave="openSubmenu = null">
                                        <!-- Submenu button -->
                                        <button
                                            class="flex justify-between items-center w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 text-sm text-left">
                                            {{ $submenu->{'title_' . app()->getLocale()} }}
                                            <svg class="w-4 h-4 flex-shrink-0 ml-auto" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>

                                        <!-- Multimenus -->
                                        <div x-show="openSubmenu === {{ $submenu->id }}" x-cloak x-transition
                                            class="absolute left-full top-0 mt-0 ml-1 w-56 text-sm max-h-96 overflow-y-scroll bg-white dark:bg-gray-900 border rounded shadow-lg dark:border-gray-800">
                                            @foreach ($submenu->multimenus as $multimenu)
                                                <a href="{{ $multimenu->link ?: localized_page_route($menu, $submenu, $multimenu) }}"
                                                    target="{{ $multimenu->link ? '_blank' : '_self' }}"
                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800">
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
