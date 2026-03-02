<div class="py-10 lg:py-20 px-4 bg-gray-100"
     x-data x-intersect.once.threshold.20="$el.classList.add('footer-in')">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            <!-- HEMIS -->
            <div class="footer-anim" style="transition-delay:0.10s;">
                <a href="https://student.sies.uz/dashboard/login" target="_blank"
                    class="card-shine group flex flex-col items-center justify-center p-6 rounded-xl overflow-hidden
                           bg-teal-800 border border-teal-700/40 hover:border-teal-500/60
                           hover:bg-teal-700 hover:-translate-y-1 transition-transform duration-300"
                    style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);">
                    <img src="{{ asset('img/icons/hemisstudent.webp') }}" alt="HEMIS"
                        class="w-16 h-16 mb-4 invert opacity-80 group-hover:opacity-100 transition-opacity">
                    <p class="text-center text-teal-100 font-medium group-hover:text-white transition-colors">
                        {{ __('messages.hemis') }}
                    </p>
                </a>
            </div>

            <!-- Dars jadvali -->
            <div class="footer-anim" style="transition-delay:0.25s;">
                <a href="https://lesson.sies.uz/" target="_blank"
                    class="card-shine group flex flex-col items-center justify-center p-6 rounded-xl overflow-hidden
                           bg-teal-800 border border-teal-700/40 hover:border-teal-500/60
                           hover:bg-teal-700 hover:-translate-y-1 transition-transform duration-300"
                    style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);">
                    <img src="{{ asset('img/icons/schedule.webp') }}" alt="Dars jadvali"
                        class="w-16 h-16 mb-4 invert opacity-80 group-hover:opacity-100 transition-opacity">
                    <p class="text-center text-teal-100 font-medium group-hover:text-white transition-colors">
                        {{ __('messages.schedule') }}
                    </p>
                </a>
            </div>

            <!-- Masofaviy ta'lim -->
            <div class="footer-anim" style="transition-delay:0.40s;">
                <a href="https://mtt.sies.uz/" target="_blank"
                    class="card-shine group flex flex-col items-center justify-center p-6 rounded-xl overflow-hidden
                           bg-teal-800 border border-teal-700/40 hover:border-teal-500/60
                           hover:bg-teal-700 hover:-translate-y-1 transition-transform duration-300"
                    style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);">
                    <img src="{{ asset('img/icons/moodle1.webp') }}" alt="Masofaviy ta'lim"
                        class="w-16 h-16 mb-4 invert opacity-80 group-hover:opacity-100 transition-opacity">
                    <p class="text-center text-teal-100 font-medium group-hover:text-white transition-colors">
                        {{ __('messages.online_learning') }}
                    </p>
                </a>
            </div>

            <!-- Elektron kutubxona -->
            <div class="footer-anim" style="transition-delay:0.55s;">
                <a href="https://arm.sies.uz/" target="_blank"
                    class="card-shine group flex flex-col items-center justify-center p-6 rounded-xl overflow-hidden
                           bg-teal-800 border border-teal-700/40 hover:border-teal-500/60
                           hover:bg-teal-700 hover:-translate-y-1 transition-transform duration-300"
                    style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);">
                    <img src="{{ asset('img/icons/library.webp') }}" alt="Elektron kutubxona"
                        class="w-16 h-16 mb-4 invert opacity-80 group-hover:opacity-100 transition-opacity">
                    <p class="text-center text-teal-100 font-medium group-hover:text-white transition-colors">
                        {{ __('messages.e_library') }}
                    </p>
                </a>
            </div>

        </div>
    </div>
</div>
