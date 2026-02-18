<div class="w-full bg-gray-200 py-3 px-2 dark:bg-gray-950">
    <div class="container mx-auto">
        <div class="flex items-center justify-between">
            <div class="grid auto-cols-max grid-flow-col items-center gap-1">
                <x-icon-button>
                    <a href='{{ route('symbol.show', ['symbol' => $symbolSlugs['flag']]) }}' aria-label="Uzb flag">
                        <img src="{{ asset('img/flags/uz.png') }}" alt="Uzb flag" class="w-5 h-5" width="20px"
                            height="20px" />
                    </a>
                </x-icon-button>

                <x-icon-button>
                    <a href='{{ route('symbol.show', ['symbol' => $symbolSlugs['emblem']]) }}' aria-label="Gerb">
                        <img src="{{ asset('img/gerb.webp') }}" alt="Gerb" class="w-5 h-5" width="20px"
                            height="20px" />
                    </a>
                </x-icon-button>
                <x-icon-button>
                    <a href='{{ route('symbol.show', ['symbol' => $symbolSlugs['anthem']]) }}' aria-label="Music icon">
                        <img src="{{ asset('img/icons/002-musical-note.webp') }}" alt="Music icon"
                            class="w-4 h-4 dark:invert" width="16px" height="16px" />
                    </a>
                </x-icon-button>
            </div>

            <div class="hidden auto-cols-max grid-flow-col items-center gap-1 lg:grid">
                <x-icon-button>
                    <a href="tel:+998662311253">
                        <img src="{{ asset('img/icons/003-phone-call.webp') }}" alt="Phone icon"
                            class="w-4 dark:invert" />
                    </a>
                </x-icon-button>
                <x-icon-button>
                    <a href="mailto:sies_info@edu.uz">
                        <img src="{{ asset('img/icons/001-envelope.webp') }}" alt="Envelope icon"
                            class="w-4 dark:invert" />
                    </a>
                </x-icon-button>
                <x-icon-button>
                    <a href="/sitemap">
                        <img src="{{ asset('img/icons/004-sitemap.webp') }}" alt="Sitemap icon"
                            class="w-4 dark:invert" />
                    </a>
                </x-icon-button>
            </div>

            <div class="hidden auto-cols-max grid-flow-col items-center gap-1 text-foreground md:grid">
                <x-button>
                    <a href="https://student.sies.uz/dashboard/login"
                        class="flex items-center gap-1 text-xs font-bold uppercase" target="_blank">
                        <img src="/img/icons/001-user.webp" alt="User icon" width="16px" height="16px"
                            class="w-4 h-4 dark:invert" />
                        Hemis
                    </a>
                </x-button>
                <x-button>
                    <a href="https://arm.sies.uz/" class="flex items-center gap-1 text-xs font-bold uppercase"
                        target="_blank">
                        <img src="/img/icons/004-book.webp" alt="Book icon" width="16px" height="16px"
                            class="w-4 h-4 dark:invert" />
                        Arm
                    </a>
                </x-button>
                <x-button>
                    <a href="https://sdg.sies.uz/" class="flex items-center gap-1 text-xs font-bold uppercase"
                        target="_blank">
                        <img src="/img/icons/sdg.webp" alt="SDG icon" width="16px" height="16px"
                            class="w-4 h-4 dark:invert" />
                        SDG
                    </a>
                </x-button>
            </div>

            <div class="hidden md:grid auto-cols-max grid-flow-col items-center gap-1">
                <x-icon-button>
                    <a href="https://t.me/siesuz" target="_blank" aria-label="Telegram">
                        <img src="{{ asset('img/icons/telegram.webp') }}" alt="Telegram icon" class="w-5 h-5"
                            width="20px" height="20px" />
                    </a>
                </x-icon-button>
                <x-icon-button>
                    <a href="https://t.me/siesuz" target="_blank" aria-label="Telegram">
                        <img src="{{ asset('img/icons/facebook.webp') }}" alt="Telegram icon" class="w-5 h-5"
                            width="20px" height="20px" />
                    </a>
                </x-icon-button>
                <x-icon-button>
                    <a href="https://t.me/siesuz" target="_blank" aria-label="Telegram">
                        <img src="{{ asset('img/icons/instagram.webp') }}" alt="Telegram icon" class="w-5 h-5"
                            width="20px" height="20px" />
                    </a>
                </x-icon-button>
                <x-icon-button>
                    <a href="https://t.me/siesuz" target="_blank" aria-label="Telegram">
                        <img src="{{ asset('img/icons/youtube.webp') }}" alt="Telegram icon" class="w-5 h-5"
                            width="20px" height="20px" />
                    </a>
                </x-icon-button>
            </div>

            <div class="grid auto-cols-max grid-flow-col items-center gap-1">
                @include('components.main.search')
                @include('components.main.language-switcher')
                @include('components.main.dark-mode-toggle')

                @if (Auth::check())
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button
                                    class="flex text-sm border-2 border-transparent rounded-sm focus:outline-none focus:border-gray-300 transition">
                                    <img class="size-8 rounded-sm object-cover"
                                        src="{{ Auth::user()->profile_photo_url }}"
                                        alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                        {{ Auth::user()->name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            @php
                                $user = auth()->user();
                            @endphp

                            @if ($user && $user->hasAnyRole(['super-admin', 'admin', 'user']))
                                <x-dropdown-link href="{{ route('filament.admin.pages.dashboard') }}">
                                    {{ __('Admin') }}
                                </x-dropdown-link>
                            @endif

                            <x-dropdown-link href="{{ route('dashboard') }}">
                                {{ __('Dashboard') }}
                            </x-dropdown-link>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <x-icon-button>
                        <a href="{{ route('login') }}">
                            <img src="{{ asset('img/icons/002-enter.webp') }}" alt="User icon"
                                class="w-4 h-4 dark:invert" width="16px" height="16px" />
                        </a>
                    </x-icon-button>
                    <x-icon-button>
                        <a href="{{ route('register') }}">
                            <img src="{{ asset('img/icons/003-add-user.webp') }}" alt="User icon"
                                class="w-4 h-4 dark:invert" width="16px" height="16px" />
                        </a>
                    </x-icon-button>
                @endif
            </div>
        </div>
    </div>
</div>
