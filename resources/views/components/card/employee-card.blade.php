@props(['employee', 'locale', 'menuModel', 'submenuModel' => null, 'multimenuModel' => null, 'page'])

<div class="rounded-xl overflow-hidden bg-white dark:bg-gray-800 shadow">
    <div class="p-2">
        <x-main.image src="{{ asset('storage/' . ($employee->image ?? '')) }}" alt="{{ lc_name($employee) }}"
            class="h-56 w-full object-cover rounded-t-md" />
    </div>
    <div class="p-4 space-y-2">
        <div class="flex items-center gap-2">
            <!-- User icon -->
            <img src="{{ asset('img/icons/user-tie.webp') }}" class="w-6 h-6" alt="Icon">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ lc_name($employee) }}
            </h3>
        </div>

        <div class="flex items-center gap-2">
            <!-- Briefcase icon -->
            <img src="{{ asset('img/icons/checklist.webp') }}" class="w-6 h-6" alt="Icon">
            <p class="text-sm text-gray-600 dark:text-gray-300">
                {{ lc_position($employee) }}
            </p>
        </div>

        <div class="grid">
            <x-button class="mt-3">
                <a href="{{ localized_staff_url($menuModel, $submenuModel, $multimenuModel, $employee, $page ?? null) }}"
                    class="flex items-center gap-2">
                    {{ __('messages.read_more') }}
                    <svg class="h-3.5 w-3.5 rtl:rotate-180" aria-hidden="true" viewBox="0 0 14 10" fill="none">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                </a>
            </x-button>
        </div>
    </div>
</div>
