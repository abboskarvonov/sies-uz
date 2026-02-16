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

<section class="relative">
    {{-- Background --}}
    <div class="relative h-[500px] md:h-[600px] overflow-hidden">
        <x-main.image :eager="true" width="1920" height="700" src="{{ asset($bg) }}" alt="Hero background"
            class="absolute inset-0 h-full w-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-black/10"></div>

        {{-- Title + subtitle + CTA --}}
        <div class="relative z-10 flex h-full items-center justify-center text-center px-4">
            <div class="max-w-4xl text-white">
                <h1 class="text-3xl md:text-6xl font-bold tracking-tight">
                    {{ $title }}
                </h1>
                <p class="mt-4 text-base md:text-xl opacity-90">
                    {{ $subtitle }}
                </p>
            </div>
        </div>
    </div>

    {{-- Stats strip (overlap) --}}
    <div class="relative z-20 -mt-12 md:-mt-16 px-4">
        <div class="container mx-auto">
            <div
                class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6 rounded-2xl bg-white/80 backdrop-blur shadow-lg p-3 md:p-6 dark:bg-gray-800/80">
                {{-- Students --}}
                <div
                    class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm flex flex-col items-start group transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('img/icons/graduating-student.png') }}" class="w-6 h-6 dark:invert"
                            alt="">
                        <p class="text-sm uppercase tracking-wide text-gray-600 dark:text-gray-300">
                            {{ __('messages.students') }}</p>
                    </div>
                    <p class="mt-4 text-3xl md:text-4xl font-extrabold">
                        <span class="countup" data-target="{{ (int) ($stat['students'] ?? 0) }}">0</span>
                    </p>
                </div>


                {{-- Teachers --}}
                <div
                    class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm flex flex-col items-start group transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('img/icons/teacher.webp') }}" class="w-6 h-6 dark:invert" alt="">
                        <p class="text-sm uppercase tracking-wide text-gray-600 dark:text-gray-300">
                            {{ __('messages.teachers') }}</p>
                    </div>
                    <p class="mt-4 text-3xl md:text-4xl font-extrabold">
                        <span class="countup" data-target="{{ (int) ($stat['teachers'] ?? 0) }}">0</span>
                    </p>
                </div>

                {{-- Faculties --}}
                <div
                    class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm flex flex-col items-start group transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('img/icons/university.png') }}" class="w-6 h-6 dark:invert" alt="">
                        <p class="text-sm uppercase tracking-wide text-gray-600 dark:text-gray-300">
                            {{ __('messages.faculty') }}</p>
                    </div>
                    <p class="mt-4 text-3xl md:text-4xl font-extrabold">
                        <span class="countup" data-target="{{ (int) ($stat['faculties'] ?? 0) }}">0</span>
                    </p>
                </div>

                {{-- Departments --}}
                <div
                    class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm flex flex-col items-start group transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('img/icons/016-bookmark.webp') }}" class="w-6 h-6 dark:invert"
                            alt="">
                        <p class="text-sm uppercase tracking-wide text-gray-600 dark:text-gray-300">
                            {{ __('messages.departments') }}</p>
                    </div>
                    <p class="mt-4 text-3xl md:text-4xl font-extrabold">
                        <span class="countup" data-target="{{ (int) ($stat['departments'] ?? 0) }}">0</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
