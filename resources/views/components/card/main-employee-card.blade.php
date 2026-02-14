@props([
    'employee',
    'locale',
    'menuModel',
    'submenuModel' => null,
    'multimenuModel' => null,
    'page' => null,
    'category' => null,
])

<div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden transform transition-all duration-500">
    <div class="grid md:grid-cols-2 gap-0">
        {{-- Image Section --}}
        <div class="relative h-96 md:h-96 overflow-hidden group">
            <x-main.image src="{{ asset('storage/' . ($employee->image ?? '')) }}" alt="{{ lc_name($employee) }}"
                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" />
            <div
                class="absolute inset-0 bg-gradient-to-t from-gray-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            @if ($category)
                <div class="absolute top-4 right-4 bg-gray-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
                    {{ $category->{'title_' . app()->getLocale()} }}
                </div>
            @endif
        </div>

        {{-- Info Section --}}
        <div
            class="p-8 md:p-12 flex flex-col justify-center bg-gradient-to-br from-gray-100 to-white dark:from-gray-800 dark:to-gray-700">
            <div class="mb-6">
                <h3 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">
                    {{ lc_name($employee) }}
                </h3>
                <p class="text-lg text-gray-600 dark:text-gray-400 font-semibold">
                    {{ lc_position($employee) }}
                </p>
            </div>

            <div class="space-y-4 mb-8">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-200 mt-1 flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ lc_position($employee) }}
                    </p>
                </div>
            </div>

            <div class="grid">
                <x-button class="mt-3 py-5">
                    <a href="{{ localized_staff_url($menuModel, $submenuModel, $multimenuModel, $employee, $page ?? null) }}"
                        class="flex items-center gap-2">
                        <span>{{ __('messages.read_more') }}</span>
                        <svg class="w-5 h-5 ml-2 rtl:rotate-180" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </x-button>
            </div>
        </div>
    </div>
</div>
