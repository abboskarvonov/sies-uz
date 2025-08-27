<div class="w-full py-10 lg:py-20 px-4 lg:px-0">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl">
            <img src="/img/icons/015-bar-chart.webp" alt="Book icon" class="w-6 dark:invert">
            {{ __('messages.statistics') }}
        </h1>
        <div class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-2 md:mt-10 lg:grid-cols-4">

            {{-- Katta rasm --}}
            <div
                class="relative col-span-2 hidden h-64 gap-4 overflow-hidden rounded-2xl bg-stone-200 shadow-md dark:bg-stone-700 lg:grid">
                <img src="/img/field.webp" alt="" class="h-64 w-full object-cover">
            </div>

            {{-- Maydon --}}
            <div
                class="relative grid gap-4 overflow-hidden rounded-2xl bg-stone-200 px-6 py-12 shadow-md dark:bg-stone-600">
                <div class="flex items-center gap-2">
                    <p class="text-xl font-bold lg:text-3xl">{{ __('messages.area') }}</p>
                </div>
                <p class="text-4xl font-bold lg:text-6xl">
                    <span class="countup" data-target="{{ (int) ($stat['campus_area'] ?? 0) }}">0</span>
                    <span class="text-2xl">m<sup>2</sup></span>
                </p>
                <img src="/img/icons/square.png" alt="SamISI" class="absolute right-0 w-2/4 opacity-20 dark:invert">
            </div>

            {{-- Yashil maydon --}}
            <div
                class="relative grid gap-4 overflow-hidden rounded-2xl bg-green-200 px-6 py-12 shadow-md dark:bg-green-600">
                <div class="flex items-center gap-2">
                    <p class="text-xl font-bold lg:text-3xl">{{ __('messages.green_area') }}</p>
                </div>
                <p class="text-4xl font-bold lg:text-6xl">
                    <span class="countup" data-target="{{ (int) ($stat['green_area'] ?? 0) }}">0</span>
                    <span class="text-2xl">m<sup>2</sup></span>
                </p>
                <img src="/img/icons/tree.png" alt="SamISI" class="absolute right-0 w-2/4 opacity-20 dark:invert">
            </div>

            {{-- Fakultetlar, bo‘limlar va markazlar --}}
            <div class="relative grid gap-4 rounded-2xl bg-blue-50 p-6 shadow-md dark:bg-blue-900">
                <div class="grid">
                    <p class="text-lg font-bold text-cyan-800 dark:text-gray-100">
                        {{ __('messages.faculty') }}</p>
                    <p class="text-4xl font-bold lg:text-6xl"><span class="countup"
                            data-target="{{ (int) ($stat['faculties'] ?? 0) }}">0</span></p>
                </div>
                <div class="grid grid-cols-2">
                    <div class="grid">
                        <p class="text-sm text-cyan-800 dark:text-gray-100">
                            {{ __('messages.departments') }}</p>
                        <p class="text-2xl font-bold"><span class="countup"
                                data-target="{{ (int) ($stat['departments'] ?? 0) }}">0</span></p>
                    </div>
                    <div class="grid">
                        <p class="text-sm text-cyan-800 dark:text-gray-100">
                            {{ __('messages.section_centers') }}</p>
                        <p class="text-2xl font-bold"><span class="countup"
                                data-target="{{ (int) ($stat['centers'] ?? 0) }}">0</span></p>
                    </div>
                </div>
                <img src="/img/icons/university.png" alt="SamISI"
                    class="absolute right-6 top-1 w-16 opacity-70 dark:invert lg:w-20">
            </div>

            {{-- Hodimlar --}}
            <div class="relative grid gap-4 rounded-2xl bg-blue-50 p-6 shadow-md dark:bg-blue-900">
                <div class="grid">
                    <p class="text-lg font-bold text-cyan-800 dark:text-gray-100">
                        {{ __('messages.employees') }}</p>
                    <p class="text-4xl font-bold lg:text-6xl"><span class="countup"
                            data-target="{{ (int) ($stat['employees'] ?? 0) }}">0</span></p>
                </div>
                <div class="grid grid-cols-3">
                    <div class="grid">
                        <p class="text-sm text-cyan-800 dark:text-gray-100">
                            {{ __('messages.leadership') }}</p>
                        <p class="text-2xl font-bold">
                            <span class="countup" data-target="{{ (int) ($stat['leadership'] ?? 0) }}">0</span>
                        </p>
                    </div>
                    <div class="grid">
                        <p class="text-sm text-cyan-800 dark:text-gray-100">
                            {{ __('messages.scientific') }}</p>
                        <p class="text-2xl font-bold">
                            <span class="countup" data-target="{{ (int) ($stat['scientific'] ?? 0) }}">0</span>
                        </p>
                    </div>
                    <div class="grid">
                        <p class="text-sm text-cyan-800 dark:text-gray-100">
                            {{ __('messages.technical') }}</p>
                        <p class="text-2xl font-bold"><span class="countup"
                                data-target="{{ (int) ($stat['technical'] ?? 0) }}">0</span></p>
                    </div>
                </div>
                <img src="/img/icons/recruiting.png" alt="SamISI"
                    class="absolute right-6 top-1 w-16 opacity-70 dark:invert lg:w-20">
            </div>

            {{-- Talabalar --}}
            <div class="relative grid gap-4 rounded-2xl bg-blue-50 p-6 shadow-md dark:bg-blue-900">
                <div class="grid">
                    <p class="text-lg font-bold text-cyan-800 dark:text-gray-100">
                        {{ __('messages.students') }}</p>
                    <p class="text-4xl font-bold lg:text-6xl"><span class="countup"
                            data-target="{{ (int) ($stat['students'] ?? 0) }}">0</span></p>
                </div>
                <div class="grid grid-cols-2">
                    <div class="grid">
                        <p class="text-sm text-cyan-800 dark:text-gray-100">
                            {{ __('messages.male_students') }}</p>
                        <p class="text-2xl font-bold"><span class="countup"
                                data-target="{{ (int) ($stat['male_students'] ?? 0) }}">0</span></p>
                    </div>
                    <div class="grid">
                        <p class="text-sm text-cyan-800 dark:text-gray-100">{{ __('messages.female_students') }}
                        </p>
                        <p class="text-2xl font-bold"><span class="countup"
                                data-target="{{ (int) ($stat['female_students'] ?? 0) }}">0</span></p>
                    </div>
                </div>
                <img src="/img/icons/graduating-student.png" alt="SamISI"
                    class="absolute right-6 top-1 w-16 opacity-70 dark:invert lg:w-20">
            </div>

            {{-- O‘qituvchilar --}}
            <div class="relative grid gap-4 rounded-2xl bg-blue-50 p-6 shadow-md dark:bg-blue-900">
                <div class="grid">
                    <p class="text-lg font-bold text-cyan-800 dark:text-gray-100">
                        {{ __('messages.teachers') }}</p>
                    <p class="text-4xl font-bold lg:text-6xl"><span class="countup"
                            data-target="{{ (int) ($stat['teachers'] ?? 0) }}">0</span></p>
                </div>
                <div class="grid grid-cols-3">
                    <div class="grid">
                        <p class="text-sm text-cyan-800 dark:text-gray-100">{{ __('messages.dsc') }}</p>
                        <p class="text-2xl font-bold"><span class="countup"
                                data-target="{{ (int) ($stat['dsi'] ?? 0) }}">0</span></p>
                    </div>
                    <div class="grid">
                        <p class="text-sm text-cyan-800 dark:text-gray-100">{{ __('messages.phd') }}</p>
                        <p class="text-2xl font-bold"><span class="countup"
                                data-target="{{ (int) ($stat['phd_teachers'] ?? 0) }}">0</span></p>
                    </div>
                    <div class="grid">
                        <p class="text-sm text-cyan-800 dark:text-gray-100">
                            {{ __('messages.dotsent') }}
                        </p>
                        <p class="text-2xl font-bold"><span class="countup"
                                data-target="{{ (int) ($stat['professors'] ?? 0) }}">0</span></p>
                    </div>
                </div>
                <img src="/img/icons/teacher.webp" alt="SamISI"
                    class="absolute right-6 top-1 w-16 opacity-70 dark:invert lg:w-20">
            </div>

            {{-- Nashrlar --}}
            <div
                class="relative col-span-1 grid gap-4 rounded-2xl bg-blue-50 p-6 shadow-md dark:bg-blue-900 sm:col-span-2 lg:col-span-4">
                <div class="grid grid-cols-3 gap-2 md:grid-cols-5">
                    <div class="grid">
                        <p class="text-lg font-bold text-cyan-800 dark:text-gray-100">
                            {{ __('messages.books') }}</p>
                        <p class="text-4xl font-bold lg:text-6xl"><span class="countup"
                                data-target="{{ (int) ($stat['books'] ?? 0) }}">0</span></p>
                    </div>
                    <div class="col-span-2 grid sm:col-span-1">
                        <p class="text-sm text-cyan-800 dark:text-gray-100">
                            {{ __('messages.textbooks') }}</p>
                        <p class="text-2xl font-bold"><span class="countup"
                                data-target="{{ (int) ($stat['textbooks'] ?? 0) }}">0</span></p>
                    </div>
                    <div class="grid">
                        <p class="text-sm text-cyan-800 dark:text-gray-100">
                            {{ __('messages.manuals') }}</p>
                        <p class="text-2xl font-bold"><span class="countup"
                                data-target="{{ (int) ($stat['study'] ?? 0) }}">0</span></p>
                    </div>
                    <div class="grid">
                        <p class="text-sm text-cyan-800 dark:text-gray-100">
                            {{ __('messages.methodological') }}</p>
                        <p class="text-2xl font-bold"><span class="countup"
                                data-target="{{ (int) ($stat['methodological'] ?? 0) }}">0</span></p>
                    </div>
                    <div class="grid">
                        <p class="text-sm text-cyan-800 dark:text-gray-100">
                            {{ __('messages.monograph') }}</p>
                        <p class="text-2xl font-bold"><span class="countup"
                                data-target="{{ (int) ($stat['monograph'] ?? 0) }}">0</span></p>
                    </div>
                </div>
                <img src="/img/icons/library.png" alt="SamISI"
                    class="absolute right-6 top-1 w-16 opacity-70 dark:invert lg:w-20">
            </div>

        </div>
    </div>
</div>
