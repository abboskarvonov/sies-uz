<x-main-layout :metaTitle="$metaTitle" :metaDescription="$metaDescription" :metaImage="$metaImage" :canonical="$canonical">
    @if ($multimenus->previousPageUrl())
        @push('head_links')<link rel="prev" href="{{ $multimenus->previousPageUrl() }}">@endpush
    @endif
    @if ($multimenus->nextPageUrl())
        @push('head_links')<link rel="next" href="{{ $multimenus->nextPageUrl() }}">@endpush
    @endif
    <x-breadcrumb :menu="$menuModel" :submenu="$submenuModel" :multimenu="null" />

    <div class="px-4 lg:px-0 py-10" x-data x-intersect.once.threshold.10="$el.classList.add('footer-in')">
        <div class="container mx-auto">
            <h1
                class="flex items-center gap-3 text-xl font-medium uppercase md:text-2xl text-teal-800 mb-8 footer-anim footer-anim-d1">
                <span
                    class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-700/10 border border-teal-700/20 shrink-0">
                    <img src="{{ asset('img/icons/010-ssd.webp') }}" alt="Icon" class="w-5 h-5" />
                </span>
                {{ lc_title($submenuModel) }}
            </h1>

            @if ($multimenus->isEmpty())
                <div class="rounded-2xl bg-gray-200 border border-gray-300 p-10 text-center">
                    <p class="text-gray-400">Hozircha ma'lumotlar tayyorlanmoqda.</p>
                </div>
            @else
                <div class="grid grid-cols-4 gap-6">
                    <div class="col-span-4 md:col-span-3">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($multimenus as $mm)
                                @php
                                    $title = lc_title($mm);
                                    $img = $mm->imageUrl() ?: asset('img/subcategory.webp');
                                    $url = localized_page_route($menuModel, $submenuModel, $mm);
                                @endphp
                                <div class="footer-anim"
                                    style="transition-delay: {{ number_format(0.1 + min($loop->index, 8) * 0.1, 2) }}s;">
                                    <a href="{{ $url }}"
                                        class="card-shine group flex flex-col overflow-hidden rounded-2xl
                                               bg-gray-100 border border-gray-200 hover:border-teal-800
                                               hover:-translate-y-1 transition-transform duration-300">
                                        <div class="relative overflow-hidden">
                                            <x-main.image :src="$img" :alt="$title"
                                                class="h-72 w-full object-cover transition duration-500 group-hover:scale-[1.04]" />
                                            <div
                                                class="absolute inset-0 bg-linear-to-t from-teal-700/50 to-transparent">
                                            </div>
                                        </div>
                                        <div class="p-5 flex flex-col gap-3 flex-1">
                                            <h3
                                                class="text-base font-semibold text-center text-gray-800 group-hover:text-teal-800 transition-colors leading-snug">
                                                {{ $title }}
                                            </h3>
                                            <span class="card-shine relative inline-flex items-center justify-center gap-1.5 overflow-hidden mt-auto
                                                         px-3.5 py-1.5 rounded-lg text-sm font-medium
                                                         bg-teal-700/10
                                                         border border-teal-700/20 group-hover:border-teal-700/40
                                                         text-teal-700 group-hover:text-teal-800 transition-colors duration-300">
                                                {{ __('messages.read_more') }}
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 14 10">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M1 5h12m0 0L9 1m4 4L9 9" />
                                                </svg>
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-8">{{ $multimenus->links() }}</div>
                    </div>
                    <x-main.sidebar />
                </div>
            @endif
        </div>
    </div>
</x-main-layout>
