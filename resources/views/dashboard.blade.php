<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    @php
        $user = Auth::user();
        $initial = Str::upper(Str::substr($user->name ?? 'U', 0, 1));
        $hour = now()->hour;
        $greeting = match (true) {
            $hour < 6 => 'Xayrli tun',
            $hour < 12 => 'Xayrli tong',
            $hour < 17 => 'Xayrli kun',
            $hour < 21 => 'Xayrli kech',
            default => 'Xayrli tun',
        };

        static $stat = null;
        static $settings = null;
        $stat ??= \App\Models\SiteStat::first();
        $settings ??= \App\Models\SiteSettings::first();

        // So'nggi yangiliklar
$latestNews = \App\Models\Page::where('page_type', 'blog')
    ->where('status', true)
    ->latest('date')
    ->limit(6)
    ->get([
        'id',
        'title_uz',
        'title_ru',
        'title_en',
        'date',
        'image',
        'slug_uz',
        'slug_ru',
        'slug_en',
        'menu_id',
        'submenu_id',
        'multimenu_id',
        'views',
    ]);

// Fakultetlar
$faculties = \App\Models\Page::where('page_type', 'faculty')
    ->where('status', true)
    ->orderBy('order')
    ->get([
        'id',
        'title_uz',
        'title_ru',
        'title_en',
        'image',
        'slug_uz',
        'slug_ru',
        'slug_en',
        'menu_id',
        'submenu_id',
        'multimenu_id',
            ]);
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- ╔══════════════════════════════════╗ --}}
            {{-- ║       WELCOME BANNER             ║ --}}
            {{-- ╚══════════════════════════════════╝ --}}
            <div class="relative overflow-hidden rounded-2xl shadow-lg"
                style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 40%, #134e4a 100%);">
                <div class="pointer-events-none absolute -top-16 -right-16 w-64 h-64 rounded-full opacity-20"
                    style="background: radial-gradient(circle, #99f6e4, transparent);"></div>
                <div class="pointer-events-none absolute -bottom-10 -left-10 w-56 h-56 rounded-full opacity-10"
                    style="background: radial-gradient(circle, #5eead4, transparent);"></div>
                <svg class="pointer-events-none absolute inset-0 w-full h-full opacity-[0.04]"
                    xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="dots" width="24" height="24" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1.5" fill="white" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#dots)" />
                </svg>
                <div
                    class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 p-8">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl ring-2 ring-white/30 shrink-0 overflow-hidden">
                            @if ($user->profile_photo_path)
                                <img src="{{ $user->profile_photo_url }}"
                                     alt="{{ $user->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-white/20 backdrop-blur flex items-center justify-center
                                            text-white font-extrabold text-2xl">
                                    {{ $initial }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="text-teal-200 text-sm font-medium">{{ $greeting }},</p>
                            <h1 class="text-white text-2xl font-extrabold leading-tight mt-0.5">{{ $user->name ?? '—' }}
                            </h1>
                            <p class="text-teal-300 text-sm mt-1">{{ $user->email ?? '' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @if ($user && $user->hasAnyRole(['super-admin', 'admin', 'user']))
                            <a href="{{ route('filament.admin.pages.dashboard') }}"
                                class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-teal-800
                                      text-sm font-semibold shadow hover:shadow-md transition-all hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Admin panel
                            </a>
                        @endif
                        <a href="{{ route('profile.show') }}"
                            class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25
                                  text-white text-sm font-semibold border border-white/20 transition-all hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profil
                        </a>
                    </div>
                </div>
            </div>

            {{-- ╔══════════════════════════════════╗ --}}
            {{-- ║    HEMIS · ARM · SDG TUGMALAR    ║ --}}
            {{-- ╚══════════════════════════════════╝ --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-4">Platformalar</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    {{-- HEMIS --}}
                    @if ($settings?->hemis_url)
                        <a href="{{ $settings->hemis_url }}" target="_blank" rel="noopener"
                            class="group relative overflow-hidden flex items-center gap-4 p-5 rounded-2xl shadow-sm
                              border border-transparent hover:shadow-lg hover:-translate-y-1 transition-all duration-200"
                            style="background: linear-gradient(135deg, #0f766e, #0d9488);">
                            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-bold text-white text-base">HEMIS</div>
                                <div class="text-teal-200 text-xs mt-0.5">Talaba shaxsiy kabineti</div>
                            </div>
                            <svg class="w-4 h-4 text-white/40 group-hover:text-white ml-auto transition-colors shrink-0"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    @endif

                    {{-- ARM --}}
                    @if ($settings?->arm_url)
                        <a href="{{ $settings->arm_url }}" target="_blank" rel="noopener"
                            class="group relative overflow-hidden flex items-center gap-4 p-5 rounded-2xl shadow-sm
                              hover:shadow-lg hover:-translate-y-1 transition-all duration-200"
                            style="background: linear-gradient(135deg, #1d4ed8, #3b82f6);">
                            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-bold text-white text-base">ARM</div>
                                <div class="text-blue-200 text-xs mt-0.5">Axborot resurs markazi</div>
                            </div>
                            <svg class="w-4 h-4 text-white/40 group-hover:text-white ml-auto transition-colors shrink-0"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    @endif

                    {{-- SDG --}}
                    @if ($settings?->sdg_url)
                        <a href="{{ $settings->sdg_url }}" target="_blank" rel="noopener"
                            class="group relative overflow-hidden flex items-center gap-4 p-5 rounded-2xl shadow-sm
                              hover:shadow-lg hover:-translate-y-1 transition-all duration-200"
                            style="background: linear-gradient(135deg, #7c3aed, #a855f7);">
                            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-bold text-white text-base">SDG</div>
                                <div class="text-purple-200 text-xs mt-0.5">Barqaror rivojlanish maqsadlari</div>
                            </div>
                            <svg class="w-4 h-4 text-white/40 group-hover:text-white ml-auto transition-colors shrink-0"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    @endif

                </div>
            </div>

            {{-- ╔══════════════════════════════════╗ --}}
            {{-- ║         STATISTIKA GRID          ║ --}}
            {{-- ╚══════════════════════════════════╝ --}}
            @if ($stat)
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-4">Institut
                        statistikasi</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">

                        @php
                            $statCards = [
                                [
                                    'val' => number_format($stat->students, 0, '.', ' '),
                                    'label' => 'Talabalar',
                                    'color' => 'teal',
                                    'badge' => 'Jami',
                                    'icon' =>
                                        'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                                ],
                                [
                                    'val' => $stat->teachers,
                                    'label' => 'Professor-o\'qituvchilar',
                                    'color' => 'indigo',
                                    'badge' => 'Prof.',
                                    'icon' =>
                                        'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                                ],
                                [
                                    'val' => $stat->faculties,
                                    'label' => 'Fakultetlar',
                                    'color' => 'amber',
                                    'badge' => "Ta'lim",
                                    'icon' =>
                                        'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                                ],
                                [
                                    'val' => $stat->departments,
                                    'label' => 'Kafedralar',
                                    'color' => 'sky',
                                    'badge' => 'Ilmiy',
                                    'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16',
                                ],
                                [
                                    'val' => $stat->centers,
                                    'label' => 'Ilmiy markazlar',
                                    'color' => 'purple',
                                    'badge' => 'Ilm',
                                    'icon' =>
                                        'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
                                ],
                                [
                                    'val' => $stat->professors,
                                    'label' => 'Professorlar',
                                    'color' => 'rose',
                                    'badge' => 'DSc',
                                    'icon' =>
                                        'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
                                ],
                                [
                                    'val' => $stat->employees,
                                    'label' => 'Jami hodimlar',
                                    'color' => 'orange',
                                    'badge' => 'Xodim',
                                    'icon' =>
                                        'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                                ],
                                [
                                    'val' => $stat->books,
                                    'label' => 'Darslik va kitoblar',
                                    'color' => 'emerald',
                                    'badge' => 'Nashr',
                                    'icon' =>
                                        'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                                ],
                            ];
                        @endphp

                        @foreach ($statCards as $card)
                            <div
                                class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow group">
                                <div class="flex items-center justify-between mb-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-{{ $card['color'] }}-50 group-hover:bg-{{ $card['color'] }}-100
                                        transition-colors flex items-center justify-center">
                                        <svg class="w-5 h-5 text-{{ $card['color'] }}-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $card['icon'] }}" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs text-{{ $card['color'] }}-600 bg-{{ $card['color'] }}-50 px-2 py-0.5 rounded-full font-medium">
                                        {{ $card['badge'] }}
                                    </span>
                                </div>
                                <div class="text-2xl font-extrabold text-gray-800">{{ $card['val'] }}</div>
                                <div class="text-xs text-gray-400 mt-1 font-medium">{{ $card['label'] }}</div>
                            </div>
                        @endforeach

                    </div>
                </div>
            @endif

            {{-- ╔══════════════════════════════════════════════════════╗ --}}
            {{-- ║   SO'NGI YANGILIKLAR  +  IJTIMOIY TARMOQLAR        ║ --}}
            {{-- ╚══════════════════════════════════════════════════════╝ --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Yangiliklar (2/3) --}}
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-widest">So'nggi yangiliklar
                        </h3>
                        <a href="/"
                            class="text-xs text-teal-600 hover:text-teal-800 font-medium transition-colors">
                            Barchasi →
                        </a>
                    </div>

                    @if ($latestNews->count())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($latestNews as $news)
                                @php
                                    $newsUrl = localized_page_route($news);
                                    $newsTitle = lc_title($news);
                                    $newsImg = $news->image
                                        ? \Illuminate\Support\Facades\Storage::url($news->image)
                                        : null;
                                @endphp
                                <a href="{{ $newsUrl }}" target="_blank" rel="noopener"
                                    class="group flex flex-col bg-white rounded-2xl shadow-sm border border-gray-100
                                      hover:shadow-md hover:border-teal-200 hover:-translate-y-0.5 transition-all duration-150 overflow-hidden">

                                    {{-- Image --}}
                                    <div class="h-36 shrink-0 overflow-hidden">
                                        @if ($newsImg)
                                            <img src="{{ $newsImg }}" alt="{{ $newsTitle }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center"
                                                style="background: linear-gradient(135deg, #0d9488, #134e4a);">
                                                <svg class="w-10 h-10 text-white/40" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Content --}}
                                    <div class="p-4 flex flex-col flex-1">
                                        <p class="text-xs text-gray-400 mb-1.5 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $news->date ? \Carbon\Carbon::parse($news->date)->format('d.m.Y') : '' }}
                                            @if ($news->views)
                                                <span class="ml-auto flex items-center gap-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    {{ number_format($news->views) }}
                                                </span>
                                            @endif
                                        </p>
                                        <h4
                                            class="text-sm font-semibold text-gray-800 group-hover:text-teal-700
                                               transition-colors line-clamp-2 flex-1 leading-snug">
                                            {{ $newsTitle }}
                                        </h4>
                                        <div class="mt-3 flex items-center gap-1 text-xs text-teal-600 font-medium">
                                            Batafsil
                                            <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-2xl p-8 text-center text-gray-400 border border-gray-100">
                            Hozircha yangiliklar mavjud emas
                        </div>
                    @endif
                </div>

                {{-- Ijtimoiy tarmoqlar (1/3) --}}
                <div class="flex flex-col gap-4">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-widest">Ijtimoiy tarmoqlar</h3>

                    @php
                        $socials = [
                            [
                                'url' => $settings?->telegram_url,
                                'name' => 'Telegram',
                                'handle' => '@samisi_channel',
                                'gradient' => 'linear-gradient(135deg,#0088cc,#00aaff)',
                                'icon' =>
                                    'M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.04 9.613c-.148.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.194 1.006.131.903.608z',
                            ],
                            [
                                'url' => $settings?->instagram_url,
                                'name' => 'Instagram',
                                'handle' => '@samisi_rasmiy',
                                'gradient' => 'linear-gradient(135deg,#833ab4,#fd1d1d,#fcb045)',
                                'icon' =>
                                    'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z',
                            ],
                            [
                                'url' => $settings?->facebook_url,
                                'name' => 'Facebook',
                                'handle' => 'SamISIchannel',
                                'gradient' => 'linear-gradient(135deg,#1877f2,#42a5f5)',
                                'icon' =>
                                    'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z',
                            ],
                            [
                                'url' => $settings?->youtube_url,
                                'name' => 'YouTube',
                                'handle' => '@SamisiRasmiy',
                                'gradient' => 'linear-gradient(135deg,#ff0000,#cc0000)',
                                'icon' =>
                                    'M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z',
                            ],
                        ];
                    @endphp

                    <div class="flex flex-col gap-3">
                        @foreach ($socials as $social)
                            @if ($social['url'])
                                <a href="{{ $social['url'] }}" target="_blank" rel="noopener"
                                    class="group flex items-center gap-4 p-4 bg-white rounded-2xl border border-gray-100
                                      shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-150">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                        style="background: {{ $social['gradient'] }};">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="{{ $social['icon'] }}" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div
                                            class="text-sm font-semibold text-gray-800 group-hover:text-teal-700 transition-colors">
                                            {{ $social['name'] }}
                                        </div>
                                        <div class="text-xs text-gray-400 truncate">{{ $social['handle'] }}</div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-teal-400 ml-auto shrink-0 transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            @endif
                        @endforeach
                    </div>

                    {{-- Profil sozlamalari quick card --}}
                    <a href="{{ route('profile.show') }}"
                        class="group flex items-center gap-3 p-4 rounded-2xl border-2 border-dashed border-gray-200
                              hover:border-teal-300 hover:bg-teal-50 transition-all duration-150 mt-1">
                        <div
                            class="w-10 h-10 rounded-xl bg-gray-100 group-hover:bg-teal-100 flex items-center justify-center shrink-0 transition-colors">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-teal-600 transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-600 group-hover:text-teal-700 transition-colors">
                                Profilni tahrirlash</div>
                            <div class="text-xs text-gray-400">Ma'lumotlaringizni yangilang</div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- ╔══════════════════════════════════╗ --}}
            {{-- ║          FAKULTETLAR             ║ --}}
            {{-- ╚══════════════════════════════════╝ --}}
            @if ($faculties->count())
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-widest">Fakultetlar</h3>
                        <span class="text-xs text-gray-400">{{ $faculties->count() }} ta</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach ($faculties as $fac)
                            @php
                                $facUrl = localized_page_route($fac);
                                $facTitle = lc_title($fac);
                                $facColors = ['teal', 'indigo', 'amber', 'sky', 'purple', 'rose', 'orange', 'emerald'];
                                $facColor = $facColors[$loop->index % count($facColors)];
                            @endphp
                            <a href="{{ $facUrl }}" target="_blank" rel="noopener"
                                class="group flex flex-col items-center text-center p-5 bg-white rounded-2xl border border-gray-100
                              shadow-sm hover:shadow-md hover:border-{{ $facColor }}-200 hover:-translate-y-1 transition-all duration-150">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-{{ $facColor }}-50 group-hover:bg-{{ $facColor }}-100
                                    flex items-center justify-center mb-3 transition-colors">
                                    <svg class="w-6 h-6 text-{{ $facColor }}-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h4
                                    class="text-xs font-semibold text-gray-700 group-hover:text-{{ $facColor }}-700
                                   transition-colors leading-snug line-clamp-3">
                                    {{ $facTitle }}
                                </h4>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
