@props([
    // Controllerdan keladigan statistikalar (fallback 0)
    'stat' => [
        'students' => 0,
        'teachers' => 0,
        'faculties' => 0,
        'departments' => 0,
    ],
    // Matnlar
    'title' => __('messages.app_name'),
    'subtitle' => __('messages.hero_text'),
    // Fon rasm
    'bg' => 'img/hero-bg-1920.webp',
])

<section class="relative overflow-hidden">
    {{-- Background with parallax effect --}}
    <div class="relative h-[600px] md:h-[700px] lg:h-[800px] overflow-hidden">
        {{-- Image with parallax --}}
        <div class="hero-bg-wrapper absolute inset-0">
            <x-main.image :eager="true" width="1920" height="800" src="{{ asset($bg) }}" alt="Hero background"
                class="absolute inset-0 h-full w-full object-cover scale-110 hero-parallax" />
        </div>

        {{-- Modern gradient overlay --}}
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/80 via-blue-800/60 to-purple-900/70"></div>

        {{-- Animated shapes (reduced opacity + blur for GPU performance) --}}
        <div class="absolute inset-0 overflow-hidden opacity-10">
            <div
                class="absolute top-20 left-10 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl animate-blob">
            </div>
            <div
                class="absolute top-40 right-10 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-4000">
            </div>
        </div>

        {{-- Content --}}
        <div class="relative z-10 flex h-full items-center justify-center text-center px-4">
            <div class="max-w-5xl">
                {{-- Badge --}}
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 mb-6 animate-fade-in">
                    <span class="flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    <span class="text-white text-sm font-medium">{{ __('messages.welcome') ?? 'Xush kelibsiz' }}</span>
                </div>

                {{-- Title with gradient --}}
                <h1
                    class="text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight text-white mb-6 animate-fade-in-up animation-delay-200">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-white via-blue-100 to-white">
                        {{ $title }}
                    </span>
                </h1>

                {{-- Subtitle --}}
                <p
                    class="mt-4 text-lg md:text-xl lg:text-2xl text-blue-100 max-w-3xl mx-auto leading-relaxed animate-fade-in-up animation-delay-400">
                    {{ $subtitle }}
                </p>
            </div>
        </div>

        {{-- Scroll indicator --}}
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-white opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>
    </div>

    {{-- Modern Stats Cards with Glass Morphism --}}
    <div class="relative z-20 -mt-20 md:-mt-24 px-4 pb-8">
        <div class="container mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                {{-- Students Card --}}
                <div
                    class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/90 to-blue-600/90 backdrop-blur-xl p-6 shadow-2xl hover:shadow-blue-500/50 transition-all duration-500 hover:-translate-y-2">
                    {{-- Shine effect --}}
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <p class="text-sm uppercase tracking-wider text-white/90 font-semibold">
                                {{ __('messages.students') }}
                            </p>
                        </div>
                        <p class="text-4xl md:text-5xl font-extrabold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['students'] ?? 0) }}">0</span>
                        </p>
                    </div>
                </div>

                {{-- Teachers Card --}}
                <div
                    class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-500/90 to-purple-600/90 backdrop-blur-xl p-6 shadow-2xl hover:shadow-purple-500/50 transition-all duration-500 hover:-translate-y-2">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <p class="text-sm uppercase tracking-wider text-white/90 font-semibold">
                                {{ __('messages.teachers') }}
                            </p>
                        </div>
                        <p class="text-4xl md:text-5xl font-extrabold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['teachers'] ?? 0) }}">0</span>
                        </p>
                    </div>
                </div>

                {{-- Faculties Card --}}
                <div
                    class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-500/90 to-pink-600/90 backdrop-blur-xl p-6 shadow-2xl hover:shadow-pink-500/50 transition-all duration-500 hover:-translate-y-2">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <p class="text-sm uppercase tracking-wider text-white/90 font-semibold">
                                {{ __('messages.faculty') }}
                            </p>
                        </div>
                        <p class="text-4xl md:text-5xl font-extrabold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['faculties'] ?? 0) }}">0</span>
                        </p>
                    </div>
                </div>

                {{-- Departments Card --}}
                <div
                    class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-500/90 to-orange-600/90 backdrop-blur-xl p-6 shadow-2xl hover:shadow-orange-500/50 transition-all duration-500 hover:-translate-y-2">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <p class="text-sm uppercase tracking-wider text-white/90 font-semibold">
                                {{ __('messages.departments') }}
                            </p>
                        </div>
                        <p class="text-4xl md:text-5xl font-extrabold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['departments'] ?? 0) }}">0</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Parallax JavaScript (wrapped in requestAnimationFrame) --}}
@push('scripts')
    <script>
        if (window.matchMedia('(prefers-reduced-motion: no-preference)').matches) {
            const parallaxElement = document.querySelector('.hero-parallax');
            if (parallaxElement) {
                let ticking = false;
                window.addEventListener('scroll', () => {
                    if (!ticking) {
                        requestAnimationFrame(() => {
                            const rate = window.pageYOffset * 0.3;
                            parallaxElement.style.transform = `translateY(${rate}px) scale(1.1)`;
                            ticking = false;
                        });
                        ticking = true;
                    }
                }, { passive: true });
            }
        }
    </script>
@endpush
