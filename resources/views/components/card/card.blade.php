@props(['page', 'menuModel', 'submenuModel' => null, 'multimenuModel' => null])

<a href="{{ localized_page_route($menuModel, $submenuModel, $multimenuModel, $page) }}"
    class="card-shine group flex flex-col h-full overflow-hidden rounded-2xl
           bg-gray-100 border border-gray-200 hover:border-teal-800
           hover:-translate-y-1 transition-transform duration-300">

    {{-- Image --}}
    <div class="relative overflow-hidden">
        <x-main.image class="h-56 w-full object-cover transition duration-500 group-hover:scale-[1.04]"
            src="{{ 'storage/' . $page->image }}" alt="{{ lc_title($page) }}"
            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px" />
        <div class="absolute inset-0 bg-linear-to-t from-black/50 to-transparent"></div>

        {{-- Meta overlay --}}
        <div class="absolute bottom-2.5 left-3 flex items-center gap-3 text-[11px] text-white">
            <span class="flex gap-1 items-center">
                <img src="/img/icons/011-clock.webp" alt="" class="w-3 invert opacity-80" />
                {{ $page->date?->format('Y-m-d') }}
            </span>
            <span class="flex gap-1 items-center">
                <img src="/img/icons/012-user.webp" alt="" class="w-3 invert opacity-80" />
                {{ $page->views ?? 0 }}
            </span>
        </div>
    </div>

    {{-- Body --}}
    <div class="flex flex-col flex-1 p-4">
        <h4
            class="font-semibold text-gray-800 group-hover:text-teal-800 transition-colors line-clamp-2 mb-2 leading-snug">
            {{ Str::limit(lc_title($page), 80) }}
        </h4>
        <p class="text-sm text-gray-500 line-clamp-3 flex-1">
            {{ Str::limit(strip_tags(html_entity_decode(lc_content($page))), 120) }}
        </p>
        <span class="card-shine relative inline-flex items-center gap-1.5 overflow-hidden mt-4
                     px-3.5 py-1.5 rounded-lg text-sm font-medium self-start
                     bg-teal-700/10
                     border border-teal-700/20 group-hover:border-teal-700/40
                     text-teal-700 group-hover:text-teal-800 transition-colors duration-300">
            {{ __('messages.read_more') }}
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M1 5h12m0 0L9 1m4 4L9 9" />
            </svg>
        </span>
    </div>
</a>
