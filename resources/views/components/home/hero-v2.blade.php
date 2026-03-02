@props([
    'stat' => ['students' => 0, 'teachers' => 0, 'faculties' => 0, 'departments' => 0],
    'title' => __('messages.app_name'),
    'subtitle' => __('messages.hero_text'),
    'bg' => 'img/hero-bg-1920.webp',
])

<section class="relative overflow-hidden bg-[#06101f]">
    <div class="relative min-h-[640px] md:min-h-[700px] lg:min-h-[740px] flex">

        {{-- ═══ CHAP PANEL ═══ --}}
        <div class="relative z-20 flex flex-col justify-center w-full lg:w-[56%] px-6 md:px-12 lg:px-20 xl:px-24 py-24 lg:py-16">

            {{-- Chap aksent chiziq: animatsion gradient --}}
            <div class="absolute left-0 top-0 bottom-0 w-[3px]"
                 style="background: linear-gradient(to bottom, transparent 0%, #3b82f6 35%, #6366f1 65%, transparent 100%);">
            </div>

            {{-- Nuqtali grid bezak --}}
            <div class="absolute inset-0 pointer-events-none opacity-[0.04]"
                style="background-image: radial-gradient(circle, #93c5fd 1px, transparent 1px);
                       background-size: 28px 28px;">
            </div>

            {{-- Mazmun --}}
            <div class="relative max-w-lg">

                {{-- Badge: pulsing dot + matn --}}
                <div class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-full border border-blue-500/25 bg-blue-500/10 mb-7 animate-fade-in">
                    <span class="relative flex h-2 w-2 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-60"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-400"></span>
                    </span>
                    <span class="text-blue-300 text-xs font-semibold uppercase tracking-[0.22em]">
                        {{ __('messages.welcome') ?? 'Xush kelibsiz' }}
                    </span>
                </div>

                {{-- Sarlavha --}}
                <h1 class="text-3xl sm:text-4xl md:text-[2.75rem] xl:text-5xl font-black text-white leading-[1.1] mb-5 animate-fade-in-up animation-delay-200">
                    {{ $title }}
                </h1>

                {{-- Tavsif --}}
                <p class="text-base md:text-[1.05rem] text-slate-400 leading-relaxed mb-10 max-w-md animate-fade-in-up animation-delay-400">
                    {{ $subtitle }}
                </p>

                {{-- Statistika (2×2) --}}
                <div class="grid grid-cols-2 gap-x-8 gap-y-6 pt-7 border-t border-white/[0.08] animate-fade-in-up animation-delay-600">
                    @foreach([
                        ['key' => 'students',    'label' => __('messages.students'),    'bar' => '#3b82f6'],
                        ['key' => 'teachers',    'label' => __('messages.teachers'),    'bar' => '#6366f1'],
                        ['key' => 'faculties',   'label' => __('messages.faculty'),     'bar' => '#06b6d4'],
                        ['key' => 'departments', 'label' => __('messages.departments'), 'bar' => '#0ea5e9'],
                    ] as $item)
                    <div class="group cursor-default pl-3 border-l-2 transition-all duration-300 hover:border-opacity-100"
                         style="border-color: {{ $item['bar'] }}60;">
                        <p class="text-[2rem] md:text-[2.25rem] font-extrabold text-white tabular-nums leading-none tracking-tight">
                            <span class="countup" data-target="{{ (int)($stat[$item['key']] ?? 0) }}">0</span><span class="text-lg font-bold" style="color: {{ $item['bar'] }};">+</span>
                        </p>
                        <p class="text-[0.78rem] text-slate-500 mt-1.5 uppercase tracking-wide group-hover:text-slate-300 transition-colors duration-200">
                            {{ $item['label'] }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ═══ Diagonal ajratgich ═══ --}}
        <div class="absolute left-[48%] top-0 bottom-0 w-36 z-30 hidden lg:block"
             style="background-color: #06101f; clip-path: polygon(0 0, 0 100%, 100% 100%);">
        </div>

        {{-- ═══ OʻNG PANEL: RASM ═══ --}}
        <div class="absolute right-0 top-0 bottom-0 left-0 lg:left-[42%] z-10">
            <x-main.image :eager="true" width="1200" height="740" src="{{ asset($bg) }}"
                alt="Hero background" class="h-full w-full object-cover object-center" />
            {{-- Brand rangiga mos ko'k tint overlay --}}
            <div class="absolute inset-0"
                 style="background: linear-gradient(to right, #06101f 0%, #06101f80 30%, #0c1a3a40 60%, transparent 100%);">
            </div>
            {{-- Pastki vignette --}}
            <div class="absolute inset-0"
                 style="background: linear-gradient(to top, #06101f 0%, transparent 40%);">
            </div>
        </div>

        {{-- Mobil: matn ustidagi qoʻshimcha overlay --}}
        <div class="absolute inset-0 z-10 lg:hidden"
             style="background: linear-gradient(to right, #06101f 50%, #06101fcc 80%, #06101f99 100%);">
        </div>
    </div>

    {{-- ═══ PASTKI DIAGONAL SEPARATOR ═══ --}}
    <div class="relative z-40 -mb-1">
        <svg viewBox="0 0 1440 48" fill="none" xmlns="http://www.w3.org/2000/svg"
             class="w-full block" style="display:block;" preserveAspectRatio="none">
            <path d="M0 48 L1440 0 L1440 48 Z" class="fill-white dark:fill-gray-900"/>
        </svg>
    </div>
</section>
