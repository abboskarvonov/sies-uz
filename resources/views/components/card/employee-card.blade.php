@props([
    'employee',
    'locale',
    'menuModel',
    'submenuModel' => null,
    'multimenuModel' => null,
    'page',
    'category' => null,
])

<div class="group">
    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden transform transition-all duration-500 hover:shadow-2xl h-full flex flex-col">
        <div class="relative h-64 overflow-hidden">
            <x-main.image src="{{ asset('storage/' . ($employee->image ?? '')) }}" alt="{{ lc_name($employee) }}"
                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" />
            <div
                class="absolute inset-0 bg-gradient-to-t from-gray-900/70 via-gray-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            @if ($category)
                <div
                    class="absolute bottom-0 left-0 right-0 p-6 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                    <p class="text-sm font-medium">
                        {{ $category->{'title_' . app()->getLocale()} }}
                    </p>
                </div>
            @endif
        </div>

        <div class="p-6 flex-grow flex flex-col">
            <div class="flex-grow space-y-3">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('img/icons/user-tie.webp') }}" class="w-6 h-6" alt="Icon">
                    <h3 class="font-bold text-gray-800 dark:text-white">
                        {{ lc_name($employee) }}
                    </h3>
                </div>

                <div class="flex items-start gap-2">
                    <img src="{{ asset('img/icons/checklist.webp') }}" class="w-6 h-6 flex-shrink-0 mt-0.5"
                        alt="Icon">
                    <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                        {{ lc_position($employee) }}
                    </p>
                </div>
            </div>

            <div class="mt-4 grid space-x-2">
                <x-button class="mt-3">
                    <a href="{{ localized_staff_url($menuModel, $submenuModel, $multimenuModel, $employee, $page ?? null) }}"
                        class="flex items-center gap-2">
                        {{ __('messages.read_more') }}
                        <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </x-button>
            </div>
        </div>
    </div>
</div>
