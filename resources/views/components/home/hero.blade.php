@props([
    'stat' => ['students' => 0, 'teachers' => 0, 'faculties' => 0, 'departments' => 0],
    'title' => __('messages.app_name'),
    'subtitle' => __('messages.hero_text'),
    'bg' => 'img/hero-bg-1920.webp',
    'galleryImages' => collect(),
])

@php
    // shuffle() har sahifa yuklanishida yangi tartib beradi
    // (cache faqat ma'lumotni saqlaydi, blade render har safar ishlaydi)
    $photos = collect($galleryImages)->filter()->shuffle()->take(6)->values();
@endphp

<section class="relative overflow-hidden" style="min-height: clamp(640px, 85vh, 820px);">

    {{-- ═══ FULL BACKGROUND IMAGE ═══ --}}
    <div class="absolute inset-0">
        <x-main.image :eager="true" width="1920" height="900" src="{{ asset($bg) }}" alt="Hero background"
            class="h-full w-full object-cover object-center" />

        {{-- Asosiy overlay — teal-950/900 tonlari --}}
        <div class="absolute inset-0"
            style="background: linear-gradient(110deg,
                rgba(4,47,46,0.75) 0%,
                rgba(17, 94, 89, 0.41) 35%,
                rgba(17, 94, 89, 0.272) 62%,
                rgba(4,47,46,0.18) 100%);">
        </div>

        {{-- Pastki vignette --}}
        <div class="absolute inset-x-0 bottom-0 h-48"
            style="background: linear-gradient(to top, rgba(4,47,46,0.65), transparent);">
        </div>
    </div>

    {{-- ═══ ASOSIY KONTENT ═══ --}}
    <div class="relative z-10 h-full flex items-center" style="min-height: clamp(640px, 85vh, 820px);">
        <div class="w-full container mx-auto px-6 md:px-10 lg:px-16 py-20">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-0 items-center">

                {{-- ══ CHAP: GLASSMORPHISM CARD ══ --}}
                <div class="relative max-w-140 hero-card-in">

                    {{-- Tashqi glow halqasi --}}
                    <div class="absolute -inset-px rounded-[28px] pointer-events-none"
                        style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.301) 0%, rgba(20, 184, 165, 0.459) 40%, rgba(255, 255, 255, 0.193) 100%);
                                filter: blur(0.5px);">
                    </div>

                    {{-- Asosiy glass panel --}}
                    <div class="relative rounded-[26px] overflow-hidden"
                        style="background: linear-gradient(145deg, rgba(17,94,89,0.72) 0%, rgba(19,78,74,0.82) 50%, rgba(4,47,46,0.78) 100%);
                                backdrop-filter: blur(36px) saturate(160%);
                                -webkit-backdrop-filter: blur(36px) saturate(160%);
                                border: 1px solid rgba(255,255,255,0.13);
                                box-shadow:
                                    0 2px 4px rgba(0,0,0,0.12),
                                    0 8px 24px rgba(0,0,0,0.25),
                                    0 32px 80px rgba(0,0,0,0.35),
                                    inset 0 1.5px 0 rgba(255,255,255,0.18),
                                    inset 0 -1px 0 rgba(255,255,255,0.04),
                                    inset 1px 0 0 rgba(255,255,255,0.06);">

                        {{-- Specular highlight (yuqori-chap nurlanish) --}}
                        <div class="absolute pointer-events-none"
                            style="top:-80px; left:-60px; width:260px; height:260px;
                                    background: radial-gradient(circle, rgba(255,255,255,0.09) 0%, transparent 65%);
                                    border-radius:50%;">
                        </div>

                        {{-- Pastki diffuz glow --}}
                        <div class="absolute pointer-events-none"
                            style="bottom:-40px; right:-40px; width:200px; height:200px;
                                    background: radial-gradient(circle, rgba(20,184,166,0.10) 0%, transparent 65%);
                                    border-radius:50%;">
                        </div>

                        {{-- ═══ 3 ta 45° diagonal shine chiziqlar ═══ --}}
                        <div class="absolute inset-0 pointer-events-none" style="z-index:1;">
                            <div
                                style="position:absolute;top:50%;left:8%;width:1px;height:220%;
                                        transform:translateY(-50%) rotate(45deg);
                                        background:linear-gradient(to bottom,transparent 0%,rgba(255,255,255,0.22) 35%,rgba(255,255,255,0.12) 65%,transparent 100%);
                                        animation:heroShine 5s ease-in-out infinite;">
                            </div>
                            <div
                                style="position:absolute;top:50%;left:46%;width:1.5px;height:340%;
                                        transform:translateY(-50%) rotate(45deg);
                                        background:linear-gradient(to bottom,transparent 0%,rgba(255,255,255,0.28) 35%,rgba(255,255,255,0.16) 65%,transparent 100%);
                                        animation:heroShine 7s 2s ease-in-out infinite;">
                            </div>
                            <div
                                style="position:absolute;top:50%;left:84%;width:1px;height:270%;
                                        transform:translateY(-50%) rotate(45deg);
                                        background:linear-gradient(to bottom,transparent 0%,rgba(255,255,255,0.18) 35%,rgba(255,255,255,0.10) 65%,transparent 100%);
                                        animation:heroShine 6s 4s ease-in-out infinite;">
                            </div>
                        </div>

                        {{-- Kontent --}}
                        <div class="relative z-10 p-8 md:p-10">

                            {{-- Badge --}}
                            <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full mb-7"
                                style="background: rgba(255,255,255,0.08);
                                        border: 1px solid rgba(255,255,255,0.15);
                                        backdrop-filter: blur(8px);
                                        -webkit-backdrop-filter: blur(8px);">
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-70"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-400"></span>
                                </span>
                                <span class="text-white/80 text-xs font-semibold uppercase tracking-[0.22em]">
                                    {{ __('messages.welcome') ?? 'Xush kelibsiz' }}
                                </span>
                            </div>

                            {{-- Sarlavha --}}
                            <h1 class="text-3xl sm:text-4xl md:text-[2.55rem] font-black leading-[1.1] mb-5"
                                data-hero-anim="100"
                                style="color: rgba(255,255,255,0.95);
                                       text-shadow: 0 2px 20px rgba(0,0,0,0.3);">
                                {{ $title }}
                            </h1>

                            {{-- Tavsif --}}
                            <p class="text-[0.93rem] leading-relaxed mb-8 max-w-sm" data-hero-anim="200"
                                style="color: rgba(255,255,255,0.58);">
                                {{ $subtitle }}
                            </p>

                            {{-- Ajratgich --}}
                            <div class="h-px mb-7"
                                style="background: linear-gradient(90deg, rgba(20,184,166,0.35), rgba(255,255,255,0.08), transparent);">
                            </div>

                            {{-- Statistika — mini glass kartalar --}}
                            <div class="grid grid-cols-2 gap-3" data-hero-anim="300">
                                @foreach ([['key' => 'students', 'label' => __('messages.students'), 'accent' => 'rgba(20,184,166,0.80)', 'glow' => 'rgba(20,184,166,0.15)'], ['key' => 'teachers', 'label' => __('messages.teachers'), 'accent' => 'rgba(45,212,191,0.80)', 'glow' => 'rgba(45,212,191,0.12)'], ['key' => 'faculties', 'label' => __('messages.faculty'), 'accent' => 'rgba(94,234,212,0.80)', 'glow' => 'rgba(94,234,212,0.12)'], ['key' => 'departments', 'label' => __('messages.departments'), 'accent' => 'rgba(153,246,228,0.80)', 'glow' => 'rgba(153,246,228,0.12)']] as $item)
                                    <div class="card-shine group relative rounded-2xl px-4 py-3.5 cursor-default overflow-hidden transition-all duration-300 hover:-translate-y-0.5"
                                        style="background: rgba(255,255,255,0.05);
                                            border: 1px solid rgba(255, 255, 255, 0.193);
                                            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.197);">

                                        {{-- Hover glow --}}
                                        <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                                            style="background: radial-gradient(ellipse at 50% 100%, {{ $item['glow'] }}, transparent 70%);">
                                        </div>

                                        {{-- Aksent chiziq (yuqori) --}}
                                        <div class="absolute top-0 left-4 right-4 h-px rounded-full"
                                            style="background: linear-gradient(90deg, transparent, {{ $item['accent'] }}, transparent);">
                                        </div>

                                        <p class="text-[1.85rem] font-extrabold tabular-nums leading-none"
                                            style="color: rgba(255,255,255,0.92);">
                                            <span class="countup"
                                                data-target="{{ (int) ($stat[$item['key']] ?? 0) }}">0</span><span
                                                style="color:{{ $item['accent'] }}; font-size:1rem; font-weight:700;">+</span>
                                        </p>
                                        <p class="text-[0.7rem] uppercase tracking-wider mt-1.5 transition-colors duration-200"
                                            style="color: rgba(255,255,255,0.4);">
                                            {{ $item['label'] }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>

                        </div>{{-- /kontent --}}
                    </div>{{-- /glass panel --}}
                </div>{{-- /wrapper --}}

                {{-- ══ OʻNG: 1 MARKAZ + 5 ATROFIDA ══ --}}
                @if ($photos->count() > 0)
                    <div class="relative hidden lg:flex items-center justify-center" style="height: 620px;">
                        <div class="relative shrink-0" style="width:500px; height:620px;">

                            {{-- Dekorativ halqalar (markazga nisbatan) --}}
                            <div class="absolute rounded-full pointer-events-none"
                                style="width:400px; height:400px;
                                    border:1px solid rgba(20,184,166,0.15);
                                    top:50%; left:50%; transform:translate(-50%,-50%);">
                            </div>
                            <div class="absolute rounded-full pointer-events-none"
                                style="width:520px; height:520px;
                                    border:1px solid rgba(20,184,166,0.07);
                                    top:50%; left:50%; transform:translate(-50%,-50%);">
                            </div>

                            {{-- ── 0: MARKAZ — eng katta ── --}}
                            @if ($photos->get(0))
                                <div class="hero-circle absolute overflow-hidden rounded-full" data-circle="0"
                                    style="width:230px; height:230px; top:195px; left:135px;
                                    box-shadow: 0 0 0 5px rgba(255,255,255,0.22), 0 0 0 10px rgba(20,184,166,0.15), 0 24px 60px rgba(0,0,0,0.55);">
                                    <img src="{{ $photos->get(0) }}" alt="Gallery" class="w-full h-full object-cover"
                                        loading="lazy">
                                    <div class="absolute inset-0 rounded-full"
                                        style="background:radial-gradient(circle at 28% 28%, rgba(255,255,255,0.12), transparent 55%);">
                                    </div>
                                </div>
                            @endif

                            {{-- ── 1: YUQORI MARKAZ (-90°) ── --}}
                            @if ($photos->get(1))
                                <div class="hero-circle absolute overflow-hidden rounded-full" data-circle="1"
                                    style="width:150px; height:150px; top:55px; left:180px;
                                    box-shadow: 0 0 0 4px rgba(255,255,255,0.16), 0 14px 36px rgba(0,0,0,0.5);">
                                    <img src="{{ $photos->get(1) }}" alt="Gallery" class="w-full h-full object-cover"
                                        loading="lazy">
                                    <div class="absolute inset-0 rounded-full"
                                        style="background:radial-gradient(circle at 28% 28%, rgba(255,255,255,0.1), transparent 55%);">
                                    </div>
                                </div>
                            @endif

                            {{-- ── 2: YUQORI-OʻNG (-18°) ── --}}
                            @if ($photos->get(2))
                                <div class="hero-circle absolute overflow-hidden rounded-full" data-circle="2"
                                    style="width:180px; height:180px; top:183px; left:356px;
                                    box-shadow: 0 0 0 4px rgba(255,255,255,0.16), 0 14px 36px rgba(0,0,0,0.5);">
                                    <img src="{{ $photos->get(2) }}" alt="Gallery" class="w-full h-full object-cover"
                                        loading="lazy">
                                    <div class="absolute inset-0 rounded-full"
                                        style="background:radial-gradient(circle at 28% 28%, rgba(255,255,255,0.1), transparent 55%);">
                                    </div>
                                </div>
                            @endif

                            {{-- ── 3: QUYI-OʻNG (54°) ── --}}
                            @if ($photos->get(3))
                                <div class="hero-circle absolute overflow-hidden rounded-full" data-circle="3"
                                    style="width:140px; height:140px; top:390px; left:289px;
                                    box-shadow: 0 0 0 4px rgba(255,255,255,0.16), 0 14px 36px rgba(0,0,0,0.5);">
                                    <img src="{{ $photos->get(3) }}" alt="Gallery" class="w-full h-full object-cover"
                                        loading="lazy">
                                    <div class="absolute inset-0 rounded-full"
                                        style="background:radial-gradient(circle at 28% 28%, rgba(255,255,255,0.1), transparent 55%);">
                                    </div>
                                </div>
                            @endif

                            {{-- ── 4: QUYI-CHAP (126°) ── --}}
                            @if ($photos->get(4))
                                <div class="hero-circle absolute overflow-hidden rounded-full" data-circle="4"
                                    style="width:160px; height:160px; top:390px; left:71px;
                                    box-shadow: 0 0 0 4px rgba(255,255,255,0.16), 0 14px 36px rgba(0,0,0,0.5);">
                                    <img src="{{ $photos->get(4) }}" alt="Gallery"
                                        class="w-full h-full object-cover" loading="lazy">
                                    <div class="absolute inset-0 rounded-full"
                                        style="background:radial-gradient(circle at 28% 28%, rgba(255,255,255,0.1), transparent 55%);">
                                    </div>
                                </div>
                            @endif

                            {{-- ── 5: YUQORI-CHAP (198°) ── --}}
                            @if ($photos->get(5))
                                <div class="hero-circle absolute overflow-hidden rounded-full" data-circle="5"
                                    style="width:120px; height:120px; top:183px; left:4px;
                                    box-shadow: 0 0 0 4px rgba(255,255,255,0.16), 0 14px 36px rgba(0,0,0,0.5);">
                                    <img src="{{ $photos->get(5) }}" alt="Gallery"
                                        class="w-full h-full object-cover" loading="lazy">
                                    <div class="absolute inset-0 rounded-full"
                                        style="background:radial-gradient(circle at 28% 28%, rgba(255,255,255,0.1), transparent 55%);">
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function () {
    var section = document.querySelector('.hero-circle') && document.querySelector('.hero-circle').closest('section');
    if (!section) return;
    var circlesWrap = section.querySelector('[style*="height: 620px"]');
    if (!circlesWrap) return;
    var ob = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            section.classList.toggle('hero-circles-paused', !e.isIntersecting);
        });
    }, { threshold: 0 });
    ob.observe(section);
})();
</script>
@endpush

@push('styles')
    <style>
        /* ═══ Glass card — butun karta kirish animatsiyasi ═══ */
        .hero-card-in {
            opacity: 0;
            transform: translateY(48px) scale(0.96);
            animation: heroCardIn 0.9s 0.08s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        @keyframes heroCardIn {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ═══ Karta ichki elementlar — staggered fade-up ═══ */
        [data-hero-anim] {
            opacity: 0;
            transform: translateY(16px);
            animation: heroFadeUp 0.65s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        [data-hero-anim="0"] {
            animation-delay: 0.30s;
        }

        [data-hero-anim="100"] {
            animation-delay: 0.44s;
        }

        [data-hero-anim="200"] {
            animation-delay: 0.56s;
        }

        [data-hero-anim="300"] {
            animation-delay: 0.68s;
        }

        @keyframes heroFadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ═══ Circle kirish: scale + fade ═══ */
        .hero-circle {
            opacity: 0;
            transform: scale(0.25);
            animation: heroCircleIn 0.75s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .hero-circle[data-circle="0"] {
            animation-delay: 0.55s;
        }

        .hero-circle[data-circle="1"] {
            animation-delay: 0.75s;
        }

        .hero-circle[data-circle="2"] {
            animation-delay: 0.9s;
        }

        .hero-circle[data-circle="3"] {
            animation-delay: 1.05s;
        }

        .hero-circle[data-circle="4"] {
            animation-delay: 1.2s;
        }

        @keyframes heroCircleIn {
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* ═══ Kirganidan keyin float (suzish) ═══ */
        .hero-circle[data-circle="0"] {
            animation: heroCircleIn 0.85s 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards, heroFloat1 9s 1.3s ease-in-out infinite;
        }

        .hero-circle[data-circle="1"] {
            animation: heroCircleIn 0.70s 0.80s cubic-bezier(0.34, 1.56, 0.64, 1) forwards, heroFloat2 7s 1.5s ease-in-out infinite;
        }

        .hero-circle[data-circle="2"] {
            animation: heroCircleIn 0.70s 0.95s cubic-bezier(0.34, 1.56, 0.64, 1) forwards, heroFloat3 8s 1.65s ease-in-out infinite;
        }

        .hero-circle[data-circle="3"] {
            animation: heroCircleIn 0.70s 1.10s cubic-bezier(0.34, 1.56, 0.64, 1) forwards, heroFloat1 6s 1.8s ease-in-out infinite;
        }

        .hero-circle[data-circle="4"] {
            animation: heroCircleIn 0.70s 1.25s cubic-bezier(0.34, 1.56, 0.64, 1) forwards, heroFloat2 9s 2.0s ease-in-out infinite;
        }

        .hero-circle[data-circle="5"] {
            animation: heroCircleIn 0.70s 1.40s cubic-bezier(0.34, 1.56, 0.64, 1) forwards, heroFloat3 7s 2.1s ease-in-out infinite;
        }

        @keyframes heroFloat1 {

            0%,
            100% {
                transform: translateY(0px) scale(1);
            }

            50% {
                transform: translateY(-14px) scale(1);
            }
        }

        @keyframes heroFloat2 {

            0%,
            100% {
                transform: translateY(0px) scale(1);
            }

            50% {
                transform: translateY(-9px) scale(1);
            }
        }

        @keyframes heroFloat3 {

            0%,
            100% {
                transform: translateY(0px) scale(1);
            }

            50% {
                transform: translateY(-11px) scale(1);
            }
        }

        /* ═══ Statik shine chiziqlar pulsatsiyasi ═══ */
        @keyframes heroShine {

            0%,
            100% {
                opacity: 0.35;
            }

            50% {
                opacity: 1;
            }
        }

        /* ═══ Motion reduce ═══ */
        @media (prefers-reduced-motion: reduce) {

            .hero-card-in,
            [data-hero-anim],
            .hero-circle {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }
        }

        @media (max-width: 1023px) {
            .hero-circle {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }
        }

        /* Stop float animations when hero is off-screen */
        .hero-circles-paused .hero-circle {
            animation-play-state: paused !important;
        }
    </style>
@endpush
