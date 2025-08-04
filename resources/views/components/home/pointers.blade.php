<div class="w-full py-10 lg:py-20 px-4 lg:px-0">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl">
            <img src="/img/icons/015-bar-chart.webp" alt="Book icon" class="w-6 dark:invert">
            {{ $translations['pointers'] ?? 'Ko‘rsatkichlar' }}
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
                    <p class="text-xl font-bold lg:text-3xl">{{ $translations['area'] ?? 'Maydon' }}</p>
                </div>
                <p class="text-4xl font-bold lg:text-6xl">
                    {{ number_format($stat['campus_area'] ?? 0, 0, '.', ' ') }}
                    <span class="text-2xl">m<sup>2</sup></span>
                </p>
                <img src="/img/icons/square.png" alt="SamISI" class="absolute right-0 w-2/4 opacity-20 dark:invert">
            </div>

            {{-- Yashil maydon --}}
            <div
                class="relative grid gap-4 overflow-hidden rounded-2xl bg-green-200 px-6 py-12 shadow-md dark:bg-green-600">
                <div class="flex items-center gap-2">
                    <p class="text-xl font-bold lg:text-3xl">{{ $translations['green_area'] ?? 'Yashil hudud' }}</p>
                </div>
                <p class="text-4xl font-bold lg:text-6xl">
                    {{ number_format($stat['green_area'] ?? 0, 0, '.', ' ') }}
                    <span class="text-2xl">m<sup>2</sup></span>
                </p>
                <img src="/img/icons/tree.png" alt="SamISI" class="absolute right-0 w-2/4 opacity-20 dark:invert">
            </div>

            {{-- Fakultetlar, bo‘limlar va markazlar --}}
            <div class="relative grid gap-4 rounded-2xl bg-blue-50 p-6 shadow-md dark:bg-blue-900">
                <div class="grid">
                    <p class="text-lg font-bold text-cyan-800 dark:text-gray-100">
                        {{ $translations['faculity'] ?? 'Fakultetlar' }}</p>
                    <p class="text-4xl font-bold lg:text-6xl">{{ number_format($stat['faculties'] ?? 0) }}</p>
                </div>
                <div class="grid grid-cols-2">
                    <div class="grid">
                        <p class="text-md text-cyan-800 dark:text-gray-100">
                            {{ $translations['departments'] ?? 'Bo‘limlar' }}</p>
                        <p class="text-2xl font-bold">{{ number_format($stat['departments'] ?? 0) }}</p>
                    </div>
                    <div class="grid">
                        <p class="text-md text-cyan-800 dark:text-gray-100">
                            {{ $translations['centers'] ?? 'Markazlar' }}</p>
                        <p class="text-2xl font-bold">{{ number_format($stat['centers'] ?? 0) }}</p>
                    </div>
                </div>
                <img src="/img/icons/university.png" alt="SamISI"
                    class="absolute right-6 top-1 w-16 opacity-70 dark:invert lg:w-20">
            </div>

            {{-- Hodimlar --}}
            <div class="relative grid gap-4 rounded-2xl bg-blue-50 p-6 shadow-md dark:bg-blue-900">
                <div class="grid">
                    <p class="text-lg font-bold text-cyan-800 dark:text-gray-100">
                        {{ $translations['all_employees'] ?? 'Xodimlar' }}</p>
                    <p class="text-4xl font-bold lg:text-6xl">{{ number_format($stat['employees'] ?? 0) }}</p>
                </div>
                <div class="grid grid-cols-3">
                    <div class="grid">
                        <p class="text-md text-cyan-800 dark:text-gray-100">
                            {{ $translations['rahbariyat'] ?? 'Rahbariyat' }}</p>
                        <p class="text-2xl font-bold">{{ number_format($stat['leadership'] ?? 0) }}</p>
                    </div>
                    <div class="grid">
                        <p class="text-md text-cyan-800 dark:text-gray-100">
                            {{ $translations['ilmiy'] ?? 'Ilmiy xodimlar' }}</p>
                        <p class="text-2xl font-bold">{{ number_format($stat['scientific'] ?? 0) }}</p>
                    </div>
                    <div class="grid">
                        <p class="text-md text-cyan-800 dark:text-gray-100">
                            {{ $translations['texnik'] ?? 'Texnik xodimlar' }}</p>
                        <p class="text-2xl font-bold">{{ number_format($stat['technical'] ?? 0) }}</p>
                    </div>
                </div>
                <img src="/img/icons/recruiting.png" alt="SamISI"
                    class="absolute right-6 top-1 w-16 opacity-70 dark:invert lg:w-20">
            </div>

            {{-- Talabalar --}}
            <div class="relative grid gap-4 rounded-2xl bg-blue-50 p-6 shadow-md dark:bg-blue-900">
                <div class="grid">
                    <p class="text-lg font-bold text-cyan-800 dark:text-gray-100">
                        {{ $translations['all_students'] ?? 'Talabalar' }}</p>
                    <p class="text-4xl font-bold lg:text-6xl">{{ number_format($stat['students'] ?? 0) }}</p>
                </div>
                <div class="grid grid-cols-2">
                    <div class="grid">
                        <p class="text-md text-cyan-800 dark:text-gray-100">
                            {{ $translations['boys'] ?? 'O‘g‘il bolalar' }}</p>
                        <p class="text-2xl font-bold">{{ number_format($stat['male_students'] ?? 0) }}</p>
                    </div>
                    <div class="grid">
                        <p class="text-md text-cyan-800 dark:text-gray-100">{{ $translations['girls'] ?? 'Qizlar' }}
                        </p>
                        <p class="text-2xl font-bold">{{ number_format($stat['female_students'] ?? 0) }}</p>
                    </div>
                </div>
                <img src="/img/icons/graduating-student.png" alt="SamISI"
                    class="absolute right-6 top-1 w-16 opacity-70 dark:invert lg:w-20">
            </div>

            {{-- O‘qituvchilar --}}
            <div class="relative grid gap-4 rounded-2xl bg-blue-50 p-6 shadow-md dark:bg-blue-900">
                <div class="grid">
                    <p class="text-lg font-bold text-cyan-800 dark:text-gray-100">
                        {{ $translations['teachers'] ?? 'O‘qituvchilar' }}</p>
                    <p class="text-4xl font-bold lg:text-6xl">{{ number_format($stat['teachers'] ?? 0) }}</p>
                </div>
                <div class="grid grid-cols-3">
                    <div class="grid">
                        <p class="text-md text-cyan-800 dark:text-gray-100">{{ $translations['doktr'] ?? 'DSc' }}</p>
                        <p class="text-2xl font-bold">{{ number_format($stat['dsi'] ?? 0) }}</p>
                    </div>
                    <div class="grid">
                        <p class="text-md text-cyan-800 dark:text-gray-100">{{ $translations['nomzod'] ?? 'PhD' }}</p>
                        <p class="text-2xl font-bold">{{ number_format($stat['phd_teachers'] ?? 0) }}</p>
                    </div>
                    <div class="grid">
                        <p class="text-md text-cyan-800 dark:text-gray-100">{{ $translations['dotsent'] ?? 'Dotsent' }}
                        </p>
                        <p class="text-2xl font-bold">{{ number_format($stat['professors'] ?? 0) }}</p>
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
                            {{ $translations['nashrlar'] ?? 'Nashrlar' }}</p>
                        <p class="text-4xl font-bold lg:text-6xl">{{ number_format($stat['books'] ?? 0) }}</p>
                    </div>
                    <div class="col-span-2 grid sm:col-span-1">
                        <p class="text-md text-cyan-800 dark:text-gray-100">
                            {{ $translations['darslik'] ?? 'Darsliklar' }}</p>
                        <p class="text-2xl font-bold">{{ number_format($stat['textbooks'] ?? 0) }}</p>
                    </div>
                    <div class="grid">
                        <p class="text-md text-cyan-800 dark:text-gray-100">
                            {{ $translations['uquv'] ?? 'O‘quv qo‘llanmalar' }}</p>
                        <p class="text-2xl font-bold">{{ number_format($stat['study'] ?? 0) }}</p>
                    </div>
                    <div class="grid">
                        <p class="text-md text-cyan-800 dark:text-gray-100">
                            {{ $translations['uslubiy'] ?? 'Uslubiy qo‘llanmalar' }}</p>
                        <p class="text-2xl font-bold">{{ number_format($stat['methodological'] ?? 0) }}</p>
                    </div>
                    <div class="grid">
                        <p class="text-md text-cyan-800 dark:text-gray-100">
                            {{ $translations['monografiya'] ?? 'Monografiyalar' }}</p>
                        <p class="text-2xl font-bold">{{ number_format($stat['monograph'] ?? 0) }}</p>
                    </div>
                </div>
                <img src="/img/icons/library.png" alt="SamISI"
                    class="absolute right-6 top-1 w-16 opacity-70 dark:invert lg:w-20">
            </div>

        </div>
    </div>
</div>
