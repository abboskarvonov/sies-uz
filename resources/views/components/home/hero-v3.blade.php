@props([
    'stat' => ['students' => 0, 'teachers' => 0, 'faculties' => 0, 'departments' => 0],
    'title' => __('messages.app_name'),
    'subtitle' => __('messages.hero_text'),
    'bg' => 'img/hero-bg-1920.webp',
])

{{--
    VARIANT 3 — CINEMATIC FULLSCREEN
    To'liq ekran balandligi, matn pastki-chap burchakda
    Film poster uslubi: kuchli vignette, oltin aksent rang
    Stats: pastda minimal qator
--}}

<section class="relative overflow-hidden" style="min-height: min(92vh, 820px);">

    {{-- ═══ FON RASM (full cover) ═══ --}}
    <div class="absolute inset-0">
        <x-main.image :eager="true" width="1920" height="900" src="{{ asset($bg) }}"
            alt="Hero background" class="h-full w-full object-cover object-center" />

        {{-- Kuchli pastki vignette --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/40 to-black/15"></div>

        {{-- Chapdan gradient --}}
        <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/20 to-transparent"></div>

        {{-- Oltin aksent chiziq (chap) --}}
        <div class="absolute left-0 top-0 bottom-0 w-[3px] bg-gradient-to-b from-transparent via-amber-400 to-transparent opacity-70"></div>
    </div>

    {{-- ═══ MAZMUN (pastki-chap) ═══ --}}
    <div class="relative z-10 h-full flex flex-col justify-end" style="min-height: min(92vh, 820px);">
        <div class="container mx-auto px-6 md:px-12 lg:px-16 pb-8 md:pb-12">

            <div class="max-w-2xl">
                {{-- Aksent yorliq --}}
                <div class="flex items-center gap-3 mb-5 animate-fade-in">
                    <span class="h-0.5 w-8 bg-amber-400"></span>
                    <span class="text-amber-400 text-xs font-semibold uppercase tracking-[0.3em]">
                        {{ __('messages.welcome') ?? 'Xush kelibsiz' }}
                    </span>
                </div>

                {{-- Katta sarlavha --}}
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black text-white leading-[1.05] mb-5 animate-fade-in-up animation-delay-200">
                    {{ $title }}
                </h1>

                {{-- Tavsif --}}
                <p class="text-base md:text-lg text-white/60 leading-relaxed max-w-xl mb-10 animate-fade-in-up animation-delay-400">
                    {{ $subtitle }}
                </p>
            </div>

            {{-- ═══ STATISTIKA: gorizontal chiziq ═══ --}}
            <div class="animate-fade-in-up animation-delay-600">
                <div class="flex flex-wrap gap-0 divide-x divide-white/15 border-t border-white/15 pt-6">
                    @foreach([
                        ['key' => 'students',    'label' => __('messages.students')],
                        ['key' => 'teachers',    'label' => __('messages.teachers')],
                        ['key' => 'faculties',   'label' => __('messages.faculty')],
                        ['key' => 'departments', 'label' => __('messages.departments')],
                    ] as $i => $item)
                    <div class="flex-1 min-w-[120px] {{ $i === 0 ? 'pr-8' : 'px-8' }} py-2 group cursor-default">
                        <p class="text-2xl md:text-3xl font-extrabold text-white tabular-nums">
                            <span class="countup" data-target="{{ (int)($stat[$item['key']] ?? 0) }}">0</span>
                            <span class="text-amber-400 text-xl">+</span>
                        </p>
                        <p class="text-xs text-white/45 uppercase tracking-widest mt-1.5 group-hover:text-white/70 transition-colors">
                            {{ $item['label'] }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll indicator (oʻng pastki burchak) --}}
    <div class="absolute bottom-6 right-8 z-20 hidden md:flex flex-col items-center gap-1.5 animate-fade-in animation-delay-600">
        <span class="text-[10px] text-white/30 uppercase tracking-[0.2em] rotate-90 origin-center mb-2">Scroll</span>
        <div class="h-10 w-px bg-gradient-to-b from-white/40 to-transparent animate-pulse"></div>
    </div>
</section>
