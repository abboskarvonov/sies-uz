<x-main-layout :metaTitle="$metaTitle" :metaDescription="$metaDescription" :metaImage="$metaImage">

    {{-- Inline breadcrumb --}}
    <div class="bg-teal-950 px-4 lg:px-0 py-5">
        <div class="container mx-auto">
            <ol class="inline-flex flex-wrap items-center gap-1.5">
                <li>
                    <a href="{{ route('home') }}"
                        class="card-shine relative inline-flex items-center justify-center w-9 h-9 rounded-lg overflow-hidden
                               border border-teal-700/40 hover:border-teal-400/60 bg-teal-800/60 backdrop-blur-md
                               hover:-translate-y-0.5 transition-transform duration-200"
                        style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);" aria-label="Home">
                        <img alt="Home" src="{{ asset('img/icons/home.webp') }}" class="w-4 invert opacity-80">
                    </a>
                </li>
                <li class="text-teal-600 select-none">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li>
                    <a href="{{ route('tags') }}"
                        class="card-shine relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg overflow-hidden
                               text-sm font-medium text-teal-100 hover:text-white
                               border border-teal-700/40 hover:border-teal-400/60 bg-teal-800/60 backdrop-blur-md
                               hover:-translate-y-0.5 transition-transform duration-200"
                        style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);">
                        {{ __('messages.tags') }}
                    </a>
                </li>
                <li class="text-teal-600 select-none">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li>
                    <span class="relative inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                                 text-white border border-teal-500/50 bg-teal-700/70 backdrop-blur-md"
                        style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.10);">
                        #{{ ucfirst($tag->name) }}
                    </span>
                </li>
            </ol>
        </div>
    </div>

    <div class="bg-gray-100 px-4 lg:px-0 py-10" x-data
        x-intersect.once.threshold.10="$el.classList.add('footer-in')">
        <div class="container mx-auto">
            <h1 class="flex items-center gap-3 text-xl font-medium md:text-2xl text-teal-800 mb-8 footer-anim footer-anim-d1">
                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-700/10 border border-teal-700/20 shrink-0">
                    <img src="{{ asset('img/icons/hastag.webp') }}" alt="Icon" class="w-5" />
                </span>
                #{{ $tag->name }} {{ __('messages.tag_text') }}
            </h1>

            <div class="grid grid-cols-4 gap-6">
                <div class="col-span-4 md:col-span-3">
                    @if ($pages->count())
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($pages as $page)
                                <div class="footer-anim h-full"
                                    style="transition-delay: {{ number_format(min($loop->index, 6) * 0.08, 2) }}s;">
                                    <a href="{{ localized_page_route($page->menu, $page->submenu, $page->multimenu, $page) }}"
                                        class="card-shine group flex flex-col h-full overflow-hidden rounded-2xl
                                               bg-gray-100 border border-gray-200 hover:border-teal-800
                                               hover:-translate-y-1 transition-transform duration-300">
                                        <div class="relative overflow-hidden">
                                            <x-main.image class="h-48 w-full object-cover transition duration-500 group-hover:scale-[1.04]"
                                                src="{{ asset('storage/' . $page->image) }}" />
                                            <div class="absolute inset-0 bg-linear-to-t from-black/50 to-transparent"></div>
                                            <div class="absolute bottom-2.5 left-3 flex items-center gap-3 text-[11px] text-teal-100">
                                                <span class="flex gap-1 items-center">
                                                    <img src="/img/icons/011-clock.webp" alt="" class="w-3 invert opacity-70" />
                                                    {{ $page->date?->format('Y-m-d') }}
                                                </span>
                                                <span class="flex gap-1 items-center">
                                                    <img src="/img/icons/012-user.webp" alt="" class="w-3 invert opacity-70" />
                                                    {{ $page->views ?? 0 }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col flex-1 p-4">
                                            <h4 class="font-semibold text-gray-800 group-hover:text-teal-800 transition-colors line-clamp-2 mb-2 leading-snug">
                                                {{ Str::limit(lc_title($page), 80) }}
                                            </h4>
                                            <p class="text-sm text-gray-500 line-clamp-3 flex-1">
                                                {{ Str::limit(strip_tags(lc_content($page)), 120) }}
                                            </p>
                                            <span class="card-shine relative inline-flex items-center gap-1.5 overflow-hidden mt-4
                                                         px-3.5 py-1.5 rounded-lg text-sm font-medium
                                                         bg-teal-700/10
                                                         border border-teal-700/20 group-hover:border-teal-700/40
                                                         text-teal-700 group-hover:text-teal-800 transition-colors duration-300">
                                                {{ __('messages.read_more') }}
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 14 10">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-8">{{ $pages->onEachSide(1)->links() }}</div>
                    @else
                        <div class="rounded-2xl bg-gray-200 border border-gray-300 p-10 text-center">
                            <p class="text-gray-400">{{ __('Hozircha ushbu tegga biriktirilgan sahifalar yo\'q.') }}</p>
                        </div>
                    @endif
                </div>
                <x-main.sidebar />
            </div>
        </div>
    </div>
</x-main-layout>
