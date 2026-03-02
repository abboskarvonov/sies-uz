<x-main-layout>

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
                    <span class="relative inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                                 text-white border border-teal-500/50 bg-teal-700/70 backdrop-blur-md"
                        style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.10);">
                        {{ __('messages.search') }}
                    </span>
                </li>
            </ol>
        </div>
    </div>

    <div class="bg-gray-100 px-4 lg:px-0 py-10" x-data
        x-intersect.once.threshold.10="$el.classList.add('footer-in')">
        <div class="container mx-auto">

            <h1 class="flex items-center gap-3 text-xl font-medium md:text-2xl text-teal-800 mb-8">
                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-700/10 border border-teal-700/20 shrink-0">
                    <svg class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                {{ __('messages.search') }}: <span class="text-teal-600">"{{ $query }}"</span>
            </h1>

            @if ($results && $results->isEmpty())
                <div class="rounded-2xl bg-gray-200 border border-gray-300 p-10 text-center">
                    <p class="text-gray-400">Hech qanday natija topilmadi.</p>
                </div>
            @elseif($results)
                <div class="grid grid-cols-4 gap-6">
                    <div class="col-span-4 md:col-span-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($results as $page)
                                <a href="{{ localized_page_route($page->menu, $page->submenu, $page->multimenu, $page) }}"
                                    class="card-shine group flex flex-col gap-2 rounded-2xl p-5 overflow-hidden
                                           bg-white border border-gray-200 hover:border-teal-800
                                           hover:-translate-y-1 transition-transform duration-300">
                                    <h2 class="font-semibold text-gray-800 group-hover:text-teal-800 transition-colors leading-snug line-clamp-3">
                                        {{ $page['title_' . app()->getLocale()] }}
                                    </h2>
                                    <span class="card-shine relative inline-flex items-center gap-1.5 overflow-hidden mt-auto pt-2
                                                 px-3.5 py-1.5 rounded-lg text-sm font-medium
                                                 bg-teal-700/10
                                                 border border-teal-700/20 group-hover:border-teal-700/40
                                                 text-teal-700 group-hover:text-teal-800 transition-colors duration-300">
                                        {{ __('messages.read_more') }}
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 14 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                        </svg>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-8">{{ $results->links() }}</div>
                    </div>
                    <x-main.sidebar />
                </div>
            @endif
        </div>
    </div>
</x-main-layout>
