<div class="relative z-50 w-full py-3 px-2 backdrop-blur-xl border-b bg-teal-950"
    style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.08), 0 4px 24px rgba(0,0,0,0.25);">

    <div class="container mx-auto header-glass">
        <div class="flex items-center justify-between">
            <div class="grid auto-cols-max grid-flow-col items-center gap-1 header-btn-anim" style="animation-delay:50ms">
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
                            class="w-4 h-4 invert" width="16px" height="16px" />
                    </a>
                </x-icon-button>
            </div>

            <div class="hidden auto-cols-max grid-flow-col items-center gap-1 lg:grid header-btn-anim"
                style="animation-delay:150ms">
                @if ($siteSettings?->phone_primary)
                    <x-icon-button>
                        <a href="tel:{{ $siteSettings->phone_primary }}" aria-label="Telefon">
                            <img src="{{ asset('img/icons/003-phone-call.webp') }}" alt="Phone icon"
                                class="w-4 h-4 invert" width="16px" height="16px" />
                        </a>
                    </x-icon-button>
                @endif
                @if ($siteSettings?->email_primary)
                    {{-- Security fix (CWE-200): href is built by JS from base64 data attribute --}}
                    <x-icon-button>
                        <span class="obf-email-icon" data-e="{{ base64_encode($siteSettings->email_primary) }}"
                            aria-label="Email" role="link" tabindex="0" style="cursor:pointer">
                            <img src="{{ asset('img/icons/001-envelope.webp') }}" alt="Email icon"
                                class="w-4 h-4 invert" width="16px" height="16px" />
                        </span>
                    </x-icon-button>
                @endif
                <x-icon-button>
                    <a href="/sitemap" aria-label="Sayt xaritasi">
                        {{-- Sitemap SVG --}}
                        <img src="{{ asset('img/icons/004-sitemap.webp') }}" alt="Sitemap icon" class="w-4 h-4 invert"
                            width="16px" height="16px" />
                    </a>∫
                </x-icon-button>
            </div>

            <div class="hidden auto-cols-max grid-flow-col items-center gap-1 text-foreground md:grid header-btn-anim"
                style="animation-delay:250ms">
                <x-button>
                    <a href="{{ $siteSettings?->hemis_url ?? 'https://student.sies.uz/dashboard/login' }}"
                        class="flex items-center gap-1 text-xs font-bold uppercase" target="_blank">
                        <img src="{{ asset('img/icons/001-user.webp') }}" alt="Hemis icon" class="w-4 h-4 invert"
                            width="16px" height="16px" />
                        Hemis
                    </a>
                </x-button>
                <x-button>
                    <a href="{{ $siteSettings?->arm_url ?? 'https://arm.sies.uz/' }}"
                        class="flex items-center gap-1 text-xs font-bold uppercase" target="_blank">
                        {{-- Book SVG --}}
                        <img src="{{ asset('img/icons/004-book.webp') }}" alt="Book icon" class="w-4 h-4 invert"
                            width="16px" height="16px" />
                        Arm
                    </a>
                </x-button>
                <x-button>
                    <a href="{{ $siteSettings?->sdg_url ?? 'https://sdg.sies.uz/' }}"
                        class="flex items-center gap-1 text-xs font-bold uppercase" target="_blank">
                        <img src="/img/icons/sdg.webp" alt="SDG icon" width="16px" height="16px"
                            class="w-4 h-4 invert" />
                        SDG
                    </a>
                </x-button>
            </div>

            <div class="hidden md:grid auto-cols-max grid-flow-col items-center gap-1 header-btn-anim"
                style="animation-delay:350ms">
                @if ($siteSettings?->telegram_url)
                    <x-icon-button>
                        <a href="{{ $siteSettings->telegram_url }}" target="_blank" aria-label="Telegram">
                            {{-- Telegram brand SVG --}}
                            <img src="{{ asset('img/icons/telegram.webp') }}" alt="Telegram icon" class="w-5 h-5"
                                width="20px" height="20px" />
                        </a>
                    </x-icon-button>
                @endif
                @if ($siteSettings?->facebook_url)
                    <x-icon-button>
                        <a href="{{ $siteSettings->facebook_url }}" target="_blank" aria-label="Facebook">
                            <img src="{{ asset('img/icons/facebook.webp') }}" alt="Facebook icon" class="w-5 h-5"
                                width="20px" height="20px" />
                        </a>
                    </x-icon-button>
                @endif
                @if ($siteSettings?->instagram_url)
                    <x-icon-button>
                        <a href="{{ $siteSettings->instagram_url }}" target="_blank" aria-label="Instagram">
                            <img src="{{ asset('img/icons/instagram.webp') }}" alt="Instagram icon" class="w-5 h-5"
                                width="20px" height="20px" />
                        </a>
                    </x-icon-button>
                @endif
                @if ($siteSettings?->youtube_url)
                    <x-icon-button>
                        <a href="{{ $siteSettings->youtube_url }}" target="_blank" aria-label="YouTube">
                            <img src="{{ asset('img/icons/youtube.webp') }}" alt="YouTube icon" class="w-5 h-5"
                                width="20px" height="20px" />
                        </a>
                    </x-icon-button>
                @endif
            </div>

            <div class="grid auto-cols-max grid-flow-col items-center gap-1 header-btn-anim"
                style="animation-delay:450ms">
                @include('components.main.search')
                @include('components.main.language-switcher')
                {{-- @include('components.main.dark-mode-toggle') --}}

                @if (Auth::check())
                    <x-dropdown align="right" width="48"
                        contentClasses="py-1 bg-teal-800/85 backdrop-blur-xl [&_a]:text-teal-100 [&_a:hover]:bg-teal-700/60 [&_a:hover]:text-white"
                        dropdownClasses="border border-teal-700/40 rounded-xl overflow-hidden">
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
                            <div class="block px-4 py-2 text-xs text-teal-400/70">
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

                            <div class="border-t border-teal-700/40"></div>

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
                                class="w-4 h-4 invert" width="16px" height="16px" />
                        </a>
                    </x-icon-button>
                    <x-icon-button>
                        <a href="{{ route('register') }}">
                            <img src="{{ asset('img/icons/003-add-user.webp') }}" alt="User icon"
                                class="w-4 h-4 invert" width="16px" height="16px" />
                        </a>
                    </x-icon-button>
                @endif
            </div>
        </div>
    </div>
</div>
