<nav x-data="{ open: false }" class="bg-teal-900 shadow-lg sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- ===== LEFT: Logo + Nav links ===== --}}
            <div class="flex items-center gap-6">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group">
                    <img src="/img/logo.webp" alt="SamISI" class="h-9 w-9 object-contain" />
                    <div class="hidden sm:block leading-tight">
                        <div class="text-white font-bold text-sm tracking-widest uppercase">SamISI</div>
                        <div class="text-teal-400 text-[10px] tracking-wide">Boshqaruv paneli</div>
                    </div>
                </a>

                {{-- Desktop nav links --}}
                <div class="hidden sm:flex items-center gap-1">
                    <a href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('dashboard') ? 'bg-teal-700/60 text-white' : 'text-teal-200 hover:text-white hover:bg-teal-800' }}
                              flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                </div>
            </div>

            {{-- ===== RIGHT: Admin btn + User dropdown ===== --}}
            <div class="hidden sm:flex items-center gap-2">
                @php $user = auth()->user(); @endphp

                {{-- Admin panel button --}}
                @if ($user && $user->hasAnyRole(['super-admin', 'admin', 'user']))
                    <a href="{{ route('filament.admin.pages.dashboard') }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-700 hover:bg-teal-600
                              text-white text-xs font-semibold transition-colors duration-150">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Admin panel
                    </a>
                @endif

                {{-- User dropdown --}}
                <x-dropdown align="right" width="56"
                    contentClasses="py-1 bg-white shadow-xl rounded-xl border border-gray-100">
                    <x-slot name="trigger">
                        <button
                            class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg hover:bg-teal-800 transition-colors duration-150 group">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <img class="w-8 h-8 rounded-full object-cover ring-2 ring-teal-500"
                                    src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            @else
                                <div
                                    class="w-8 h-8 rounded-full bg-teal-500 flex items-center justify-center
                                            text-white font-bold text-sm ring-2 ring-teal-600 shrink-0">
                                    {{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="text-left hidden md:block max-w-35">
                                <div class="text-sm font-medium text-white leading-tight truncate">
                                    {{ Auth::user()->name }}</div>
                                <div class="text-xs text-teal-300 leading-tight truncate">{{ Auth::user()->email }}
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-teal-400 group-hover:text-white transition-colors shrink-0"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        {{-- User info header --}}
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-0.5">Kirgan
                                foydalanuvchi</p>
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        {{-- Links --}}
                        <div class="py-1">
                            <x-dropdown-link href="{{ route('dashboard') }}"
                                class="flex! items-center gap-2.5 text-gray-700! hover:bg-teal-50! hover:text-teal-700!">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </x-dropdown-link>

                            <x-dropdown-link href="{{ route('profile.show') }}"
                                class="flex! items-center gap-2.5 text-gray-700! hover:bg-teal-50! hover:text-teal-700!">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profil
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}"
                                    class="flex! items-center gap-2.5 text-gray-700! hover:bg-teal-50! hover:text-teal-700!">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                    API Tokenlar
                                </x-dropdown-link>
                            @endif
                        </div>

                        {{-- Logout --}}
                        <div class="border-t border-gray-100 py-1">
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit()"
                                    class="flex! items-center gap-2.5 text-red-500! hover:bg-red-50!">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Chiqish
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- ===== MOBILE: Hamburger ===== --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open"
                    class="p-2 rounded-lg text-teal-300 hover:text-white hover:bg-teal-800 transition-colors">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ===== MOBILE MENU ===== --}}
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden bg-teal-950 border-t border-teal-800">

        {{-- Mobile user info --}}
        <div class="flex items-center gap-3 px-4 py-4 border-b border-teal-800">
            <div
                class="w-10 h-10 rounded-full bg-teal-500 flex items-center justify-center
                        text-white font-bold text-sm ring-2 ring-teal-600 shrink-0">
                {{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <div class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-teal-400 truncate">{{ Auth::user()->email }}</div>
            </div>
        </div>

        {{-- Mobile nav links --}}
        <div class="px-3 py-2 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'bg-teal-800 text-white' : 'text-teal-200 hover:bg-teal-800 hover:text-white' }}
                      flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('profile.show') }}"
                class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm text-teal-200 hover:bg-teal-800 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profil
            </a>

            @php $user = auth()->user(); @endphp
            @if ($user && $user->hasAnyRole(['super-admin', 'admin', 'user']))
                <a href="{{ route('filament.admin.pages.dashboard') }}"
                    class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm text-teal-200 hover:bg-teal-800 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Admin panel
                </a>
            @endif
        </div>

        {{-- Mobile logout --}}
        <div class="px-3 py-3 border-t border-teal-800">
            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <button type="submit" @click.prevent="$root.submit()"
                    class="flex items-center gap-2 w-full px-3 py-2.5 rounded-lg text-sm
                               text-red-400 hover:bg-teal-800 hover:text-red-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Chiqish
                </button>
            </form>
        </div>
    </div>
</nav>
