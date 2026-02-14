<div class="py-10 lg:py-20 px-4 bg-gray-50 dark:bg-gray-800">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- HEMIS -->
            <a href="https://student.sies.uz/dashboard/login" target="_blank"
                class="flex flex-col items-center justify-center p-6 
              bg-white dark:bg-gray-700 rounded-lg shadow 
              hover:shadow-md transition hover:bg-gray-100 dark:hover:bg-gray-900">
                <img src="{{ asset('img/icons/hemisstudent.webp') }}" alt="HEMIS" class="w-16 h-16 mb-4 dark:invert">
                <p class="text-center text-gray-700 dark:text-gray-200 font-medium">{{ __('messages.hemis') }}</p>
            </a>

            <!-- Dars jadvali -->
            <a href="https://lesson.sies.uz/"
                class="flex flex-col items-center justify-center p-6 
              bg-white dark:bg-gray-700 rounded-lg shadow 
              hover:shadow-md transition hover:bg-gray-100 dark:hover:bg-gray-900"
                target="_blank">
                <img src="{{ asset('img/icons/schedule.webp') }}" alt="Dars jadvali" class="w-16 h-16 mb-4 dark:invert">
                <p class="text-center text-gray-700 dark:text-gray-200 font-medium">{{ __('messages.schedule') }}</p>
            </a>

            <!-- Masofaviy ta'lim -->
            <a href="https://mtt.sies.uz/"
                class="flex flex-col items-center justify-center p-6 
              bg-white dark:bg-gray-700 rounded-lg shadow 
              hover:shadow-md transition hover:bg-gray-100 dark:hover:bg-gray-900"
                target="_blank">
                <img src="{{ asset('img/icons/moodle1.webp') }}" alt="Masofaviy ta'lim"
                    class="w-16 h-16 mb-4 dark:invert">
                <p class="text-center text-gray-700 dark:text-gray-200 font-medium">{{ __('messages.online_learning') }}
                </p>
            </a>

            <!-- Elektron kutubxona -->
            <a href="https://arm.sies.uz/"
                class="flex flex-col items-center justify-center p-6 
              bg-white dark:bg-gray-700 rounded-lg shadow 
              hover:shadow-md transition hover:bg-gray-100 dark:hover:bg-gray-900"
                target="_blank">
                <img src="{{ asset('img/icons/library.webp') }}" alt="Elektron kutubxona"
                    class="w-16 h-16 mb-4 dark:invert">
                <p class="text-center text-gray-700 dark:text-gray-200 font-medium">{{ __('messages.e_library') }}</p>
            </a>
        </div>
    </div>
</div>
