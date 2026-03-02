<div class="w-full py-10 lg:py-20 px-4 lg:px-0 bg-white" x-data
    x-intersect.once.threshold.10="$el.classList.add('footer-in')">
    <div class="container mx-auto">
        <h1
            class="flex items-center gap-3 text-xl font-medium uppercase md:text-2xl mb-5 text-teal-800 footer-anim footer-anim-d1">
            <span
                class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-700/10 border border-teal-700/20 shrink-0">
                <img src="/img/icons/015-bar-chart.webp" alt="Book icon" class="w-5">
            </span>
            {{ __('messages.statistics') }}
        </h1>

        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 md:mt-8 lg:grid-cols-4">

            {{-- Katta rasm --}}
            <div class="footer-anim relative col-span-2 hidden h-64 overflow-hidden rounded-2xl lg:block
                        border border-teal-700/40"
                style="transition-delay:0.10s; box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);">
                <img src="/img/field.webp" alt="" class="h-64 w-full object-cover">
                <div class="absolute inset-0 bg-linear-to-t from-teal-950/60 to-transparent pointer-events-none"></div>
            </div>

            {{-- Maydon --}}
            <div class="card-shine footer-anim relative overflow-hidden rounded-2xl px-6 py-10
                        bg-neutral-700 backdrop-blur-md border border-neutral-700/40"
                style="transition-delay:0.20s;">
                <div class="flex items-center gap-2 mb-3">
                    <p class="text-base font-bold text-teal-200">{{ __('messages.area') }}</p>
                </div>
                <p class="text-4xl font-bold text-white lg:text-5xl">
                    <span class="countup" data-target="{{ (int) ($stat['campus_area'] ?? 0) }}">0</span>
                    <span class="text-xl text-teal-300">m<sup>2</sup></span>
                </p>
                <img src="/img/icons/square.webp" alt=""
                    class="absolute right-0 bottom-0 w-2/4 opacity-30 invert">
            </div>

            {{-- Yashil maydon --}}
            <div class="card-shine footer-anim relative overflow-hidden rounded-2xl px-6 py-10
                        bg-emerald-800 backdrop-blur-md border border-emerald-700/40"
                style="transition-delay:0.30s;">
                <div class="flex items-center gap-2 mb-3">
                    <p class="text-base font-bold text-emerald-200">{{ __('messages.green_area') }}</p>
                </div>
                <p class="text-4xl font-bold text-white lg:text-5xl">
                    <span class="countup" data-target="{{ (int) ($stat['green_area'] ?? 0) }}">0</span>
                    <span class="text-xl text-emerald-300">m<sup>2</sup></span>
                </p>
                <img src="/img/icons/tree.webp" alt=""
                    class="absolute right-0 bottom-0 w-2/4 opacity-30 invert">
            </div>

            {{-- Fakultetlar, bo'limlar va markazlar --}}
            <div class="card-shine footer-anim relative overflow-hidden rounded-2xl p-6
                        bg-teal-700 backdrop-blur-md border border-teal-700/40"
                style="transition-delay:0.10s;">
                <div class="grid mb-4">
                    <p class="text-base font-bold text-teal-200">{{ __('messages.faculty') }}</p>
                    <p class="text-4xl font-bold text-white lg:text-5xl">
                        <span class="countup" data-target="{{ (int) ($stat['faculties'] ?? 0) }}">0</span>
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-2 border-t border-teal-700/40 pt-3">
                    <div class="grid">
                        <p class="text-xs text-teal-300">{{ __('messages.departments') }}</p>
                        <p class="text-2xl font-bold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['departments'] ?? 0) }}">0</span>
                        </p>
                    </div>
                    <div class="grid">
                        <p class="text-xs text-teal-300">{{ __('messages.section_centers') }}</p>
                        <p class="text-2xl font-bold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['centers'] ?? 0) }}">0</span>
                        </p>
                    </div>
                </div>
                <img src="/img/icons/university.webp" alt=""
                    class="absolute right-4 top-4 w-14 opacity-30 invert lg:w-16">
            </div>

            {{-- Hodimlar --}}
            <div class="card-shine footer-anim relative overflow-hidden rounded-2xl p-6
                        bg-teal-700 backdrop-blur-md border border-teal-700/40"
                style="transition-delay:0.20s;">
                <div class="grid mb-4">
                    <p class="text-base font-bold text-teal-200">{{ __('messages.employees') }}</p>
                    <p class="text-4xl font-bold text-white lg:text-5xl">
                        <span class="countup" data-target="{{ (int) ($stat['employees'] ?? 0) }}">0</span>
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-1 border-t border-teal-700/40 pt-3">
                    <div class="grid">
                        <p class="text-xs text-teal-300">{{ __('messages.leadership') }}</p>
                        <p class="text-xl font-bold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['leadership'] ?? 0) }}">0</span>
                        </p>
                    </div>
                    <div class="grid">
                        <p class="text-xs text-teal-300">{{ __('messages.scientific') }}</p>
                        <p class="text-xl font-bold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['scientific'] ?? 0) }}">0</span>
                        </p>
                    </div>
                    <div class="grid">
                        <p class="text-xs text-teal-300">{{ __('messages.technical') }}</p>
                        <p class="text-xl font-bold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['technical'] ?? 0) }}">0</span>
                        </p>
                    </div>
                </div>
                <img src="/img/icons/recruiting.webp" alt=""
                    class="absolute right-4 top-4 w-14 opacity-30 invert lg:w-16">
            </div>

            {{-- Talabalar --}}
            <div class="card-shine footer-anim relative overflow-hidden rounded-2xl p-6
                        bg-teal-700 backdrop-blur-md border border-teal-700/40"
                style="transition-delay:0.30s;">
                <div class="grid mb-4">
                    <p class="text-base font-bold text-teal-200">{{ __('messages.students') }}</p>
                    <p class="text-4xl font-bold text-white lg:text-5xl">
                        <span class="countup" data-target="{{ (int) ($stat['students'] ?? 0) }}">0</span>
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-2 border-t border-teal-700/40 pt-3">
                    <div class="grid">
                        <p class="text-xs text-teal-300">{{ __('messages.male_students') }}</p>
                        <p class="text-2xl font-bold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['male_students'] ?? 0) }}">0</span>
                        </p>
                    </div>
                    <div class="grid">
                        <p class="text-xs text-teal-300">{{ __('messages.female_students') }}</p>
                        <p class="text-2xl font-bold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['female_students'] ?? 0) }}">0</span>
                        </p>
                    </div>
                </div>
                <img src="/img/icons/graduating-student.webp" alt=""
                    class="absolute right-4 top-4 w-14 opacity-30 invert lg:w-16">
            </div>

            {{-- O'qituvchilar --}}
            <div class="card-shine footer-anim relative overflow-hidden rounded-2xl p-6
                        bg-teal-700 backdrop-blur-md border border-teal-700/40"
                style="transition-delay:0.40s;">
                <div class="grid mb-4">
                    <p class="text-base font-bold text-teal-200">{{ __('messages.teachers') }}</p>
                    <p class="text-4xl font-bold text-white lg:text-5xl">
                        <span class="countup" data-target="{{ (int) ($stat['teachers'] ?? 0) }}">0</span>
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-1 border-t border-teal-700/40 pt-3">
                    <div class="grid">
                        <p class="text-xs text-teal-300">{{ __('messages.dsc') }}</p>
                        <p class="text-xl font-bold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['dsi'] ?? 0) }}">0</span>
                        </p>
                    </div>
                    <div class="grid">
                        <p class="text-xs text-teal-300">{{ __('messages.phd') }}</p>
                        <p class="text-xl font-bold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['phd_teachers'] ?? 0) }}">0</span>
                        </p>
                    </div>
                    <div class="grid">
                        <p class="text-xs text-teal-300">{{ __('messages.dotsent') }}</p>
                        <p class="text-xl font-bold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['professors'] ?? 0) }}">0</span>
                        </p>
                    </div>
                </div>
                <img src="/img/icons/teacher.webp" alt=""
                    class="absolute right-4 top-4 w-14 opacity-30 invert lg:w-16">
            </div>

            {{-- Nashrlar --}}
            <div class="card-shine footer-anim relative col-span-1 overflow-hidden rounded-2xl p-6
                        bg-teal-900 backdrop-blur-md border border-teal-700/40
                        sm:col-span-2 lg:col-span-4"
                style="transition-delay:0.15s;">
                <div class="grid grid-cols-3 gap-4 md:grid-cols-5">
                    <div class="grid">
                        <p class="text-base font-bold text-teal-200">{{ __('messages.books') }}</p>
                        <p class="text-4xl font-bold text-white lg:text-5xl">
                            <span class="countup" data-target="{{ (int) ($stat['books'] ?? 0) }}">0</span>
                        </p>
                    </div>
                    <div class="col-span-2 grid sm:col-span-1">
                        <p class="text-xs text-teal-300">{{ __('messages.textbooks') }}</p>
                        <p class="text-2xl font-bold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['textbooks'] ?? 0) }}">0</span>
                        </p>
                    </div>
                    <div class="grid">
                        <p class="text-xs text-teal-300">{{ __('messages.manuals') }}</p>
                        <p class="text-2xl font-bold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['study'] ?? 0) }}">0</span>
                        </p>
                    </div>
                    <div class="grid">
                        <p class="text-xs text-teal-300">{{ __('messages.methodological') }}</p>
                        <p class="text-2xl font-bold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['methodological'] ?? 0) }}">0</span>
                        </p>
                    </div>
                    <div class="grid">
                        <p class="text-xs text-teal-300">{{ __('messages.monograph') }}</p>
                        <p class="text-2xl font-bold text-white">
                            <span class="countup" data-target="{{ (int) ($stat['monograph'] ?? 0) }}">0</span>
                        </p>
                    </div>
                </div>
                <img src="/img/icons/library.webp" alt=""
                    class="absolute right-6 top-3 w-14 opacity-30 invert lg:w-16">
            </div>

        </div>
    </div>
</div>
