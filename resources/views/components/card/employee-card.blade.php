@props([
    'employee',
    'locale',
    'menuModel',
    'submenuModel' => null,
    'multimenuModel' => null,
    'page',
    'category' => null,
])

<div
    class="card-shine group overflow-hidden rounded-2xl flex flex-col
            bg-white border border-gray-200 hover:border-teal-700/40
            hover:-translate-y-1 transition-transform duration-300 shadow-sm">

    {{-- Photo --}}
    <div class="relative h-64 overflow-hidden">
        <x-main.image src="{{ asset('storage/' . ($employee->image ?? '')) }}" alt="{{ lc_name($employee) }}"
            class="w-full h-full object-cover transition duration-700 group-hover:scale-110" />
        <div class="absolute inset-0 bg-linear-to-t from-black/60 via-black/20 to-transparent"></div>

        @if ($category)
            <div
                class="absolute bottom-0 left-0 right-0 p-4 text-white
                        transform translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                <p class="text-xs font-medium text-gray-200">
                    {{ $category->{'title_' . app()->getLocale()} }}
                </p>
            </div>
        @endif
    </div>

    {{-- Info --}}
    <div class="p-5 flex flex-col gap-3 flex-1">
        <div class="flex items-center gap-2">
            <img src="{{ asset('img/icons/user-tie.webp') }}" class="w-5 h-5 shrink-0" alt="">
            <h3 class="font-bold text-gray-800 leading-snug">{{ lc_name($employee) }}</h3>
        </div>

        <div class="flex items-start gap-2 flex-1">
            <img src="{{ asset('img/icons/checklist.webp') }}" class="w-4 h-4 shrink-0 mt-0.5" alt="">
            <p class="text-gray-500 text-sm leading-relaxed">{{ lc_position($employee) }}</p>
        </div>

        <a href="{{ localized_staff_url($menuModel, $submenuModel, $multimenuModel, $employee, $page ?? null) }}"
            class="card-shine relative inline-flex items-center gap-1.5 overflow-hidden mt-1
                   px-3.5 py-1.5 rounded-lg text-sm font-medium self-start
                   bg-teal-700/10
                   border border-teal-700/20 hover:border-teal-700/40
                   text-teal-700 hover:text-teal-800 transition-colors duration-300">
            {{ __('messages.read_more') }}
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>
</div>
