@props([
    'employee',
    'locale',
    'menuModel',
    'submenuModel' => null,
    'multimenuModel' => null,
    'page' => null,
    'category' => null,
])

<div class="card-shine group overflow-hidden rounded-2xl
            bg-white border border-gray-200 hover:border-teal-700/40
            transition-colors duration-300 shadow-sm">
    <div class="grid md:grid-cols-2">

        {{-- Photo --}}
        <div class="relative h-80 md:h-96 overflow-hidden">
            <x-main.image src="{{ $employee->profile_photo_path ? 'storage/' . $employee->profile_photo_path : 'img/default-avatar.webp' }}" alt="{{ $employee->name }}"
                sizes="(max-width: 768px) 100vw, 50vw"
                class="w-full h-full object-cover transition duration-700 group-hover:scale-105" />
            <div class="absolute inset-0 bg-linear-to-t from-black/50 to-transparent"></div>

            @if ($category)
                <div
                    class="absolute top-4 right-4 px-3 py-1.5 rounded-full text-xs font-semibold
                            bg-teal-700/80 border border-teal-600/40 text-white backdrop-blur-sm">
                    {{ $category->{'title_' . app()->getLocale()} }}
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="p-8 md:p-10 flex flex-col justify-center gap-5">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-1.5">{{ $employee->name }}</h3>
                <p class="text-teal-700 font-medium text-sm">{{ lc_position($employee) }}</p>
            </div>

            <div class="h-px bg-gray-200"></div>

            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-teal-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <p class="text-gray-600 text-sm leading-relaxed">{{ lc_position($employee) }}</p>
            </div>

            <a href="{{ localized_staff_url($menuModel, $submenuModel, $multimenuModel, $employee, $page ?? null) }}"
                class="card-shine relative inline-flex items-center justify-center gap-2 overflow-hidden
                       px-5 py-2.5 rounded-xl text-sm font-medium self-start
                       bg-teal-700/10
                       border border-teal-700/20 hover:border-teal-700/50
                       text-teal-700 hover:text-teal-800 transition-colors duration-300">
                {{ __('messages.read_more') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

    </div>
</div>
