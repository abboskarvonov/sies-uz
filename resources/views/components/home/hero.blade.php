{{-- <section class="relative bg-gray-200 dark:bg-gray-900 bg-cover bg-center"
    style="background-image: url('{{ asset('img/hero-bg.webp') }}');">
    <div class="absolute z-10 h-full w-full bg-white/65 dark:bg-gray-950/80"></div>
    <div class="relative z-20 mx-auto max-w-screen-xl px-4 py-14 text-center lg:px-12 lg:py-28">
        <h1
            class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-900 dark:text-white md:text-5xl lg:text-6xl">
            Samarqand iqtisodiyot va servis instituti
        </h1>
        <p
            class="my-10 rounded-2xl bg-gray-900/70 py-5 text-lg font-normal text-gray-100 shadow-md dark:bg-gray-100/80 dark:text-gray-800 sm:px-16 lg:text-xl xl:px-40">
            Samarqand iqtisodiyot va servis instituti — xizmat koʻrsatish sohasi tarmoqlari iqtisodiyoti va uni tashkil
            etish boʻyicha mutaxassislar tayyorlaydigan hamda 15 dan ortiq yo'nalishlarga ega oliy oʻquv yurti.
        </p>
    </div>
</section> --}}
{{-- resources/views/components/hero-stats.blade.php --}}
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
    'bg' => asset('img/hero-bg.webp'),
])

<section class="relative">
    {{-- Background --}}
    <div class="relative h-[500px] md:h-[600px] overflow-hidden">
        <img src="{{ $bg }}" alt="Hero background" class="absolute inset-0 h-full w-full object-cover">
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
