<div class="min-h-screen flex">

    {{-- ======== LEFT PANEL: Branded ======== --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 relative flex-col justify-between overflow-hidden p-12"
        style="background: linear-gradient(145deg, #0d9488 0%, #0f766e 35%, #134e4a 100%);">

        {{-- Decorative blobs --}}
        <div class="pointer-events-none absolute -top-32 -left-32 w-md h-112 rounded-full"
            style="background: rgba(20,184,166,0.18); filter: blur(80px);"></div>
        <div class="pointer-events-none absolute bottom-0 right-0 w-80 h-80 rounded-full"
            style="background: rgba(6,78,59,0.5); filter: blur(60px);"></div>
        <div class="pointer-events-none absolute top-1/2 left-2/3 w-56 h-56 rounded-full"
            style="background: rgba(153,246,228,0.08); filter: blur(50px);"></div>

        {{-- Geometric grid lines --}}
        <svg class="pointer-events-none absolute inset-0 w-full h-full opacity-5" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid" width="48" height="48" patternUnits="userSpaceOnUse">
                    <path d="M 48 0 L 0 0 0 48" fill="none" stroke="white" stroke-width="1" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>

        {{-- Logo & Name --}}
        <div class="relative z-10 flex items-center gap-4">
            <img src="/img/logo.webp" alt="SamISI" class="w-14 h-14 object-contain drop-shadow-lg" />
            <div>
                <div class="text-xs font-semibold tracking-[0.2em] text-teal-200 uppercase">SamISI</div>
                <div class="text-xs text-teal-300 leading-tight max-w-56">
                    Samarqand Iqtisodiyot va Servis Instituti
                </div>
            </div>
        </div>

        {{-- Center hero text --}}
        <div class="relative z-10">
            <div class="w-12 h-0.5 bg-teal-400 rounded mb-8"></div>
            <h1 class="text-white text-4xl xl:text-5xl font-extrabold leading-tight mb-5 tracking-tight">
                Bilim —<br>
                <span class="text-teal-300">Kelajak Kaliti</span>
            </h1>

            {{-- Stats --}}
            @php
                static $siteStat = null;
                $siteStat ??= \App\Models\SiteStat::first();
                $studentsNum = $siteStat?->students ?? 0;
                $teachersNum = $siteStat?->teachers ?? 0;
                $studentsDisplay = $studentsNum > 0 ? number_format($studentsNum, 0, '.', ' ') : '—';
                $teachersDisplay = $teachersNum > 0 ? $teachersNum : '—';
            @endphp
            <div class="flex gap-8 mt-10">
                <div>
                    <div class="text-2xl xl:text-3xl font-bold text-white">90+</div>
                    <div class="text-teal-300 text-xs mt-0.5 tracking-wide">Yillik tajriba</div>
                </div>
                <div class="border-l border-teal-700 pl-8">
                    <div class="text-2xl xl:text-3xl font-bold text-white">{{ $studentsDisplay }}</div>
                    <div class="text-teal-300 text-xs mt-0.5 tracking-wide">Talabalar</div>
                </div>
                <div class="border-l border-teal-700 pl-8">
                    <div class="text-2xl xl:text-3xl font-bold text-white">{{ $teachersDisplay }}</div>
                    <div class="text-teal-300 text-xs mt-0.5 tracking-wide">Professor-o'qituvchilar</div>
                </div>
            </div>
        </div>

        {{-- Bottom footer --}}
        <div class="relative z-10 text-teal-500 text-xs">
            &copy; {{ date('Y') }} SamISI. Barcha huquqlar himoyalangan.
        </div>
    </div>

    {{-- ======== RIGHT PANEL: Form ======== --}}
    <div class="w-full lg:w-7/12 xl:w-1/2 flex items-center justify-center min-h-screen bg-gray-50 px-6 py-12">
        <div class="w-full max-w-md">

            {{-- Mobile logo --}}
            <div class="flex items-center justify-center gap-3 mb-8 lg:hidden">
                <img src="/img/logo.webp" alt="SamISI" class="w-11 h-11 object-contain" />
                <div>
                    <div class="font-bold text-teal-700 text-sm uppercase tracking-widest">SamISI</div>
                    <div class="text-xs text-gray-400 leading-tight">Samarqand Iqtisodiyot va Servis Instituti</div>
                </div>
            </div>

            {{ $slot }}
        </div>
    </div>

</div>
