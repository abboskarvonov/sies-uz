{{-- resources/views/tags/index.blade.php --}}
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
                        {{ __('messages.tags') }}
                    </span>
                </li>
            </ol>
        </div>
    </div>

    <div class="bg-gray-100 px-4 lg:px-0 py-10" x-data
        x-intersect.once.threshold.10="$el.classList.add('footer-in')">
        <div class="container mx-auto">
            <h1 class="flex items-center gap-3 text-xl font-medium uppercase md:text-2xl text-teal-800 mb-8 footer-anim footer-anim-d1">
                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-700/10 border border-teal-700/20 shrink-0">
                    <img src="{{ asset('img/icons/hastag.webp') }}" alt="Icon" class="w-5" />
                </span>
                {{ __('messages.tags') }}
            </h1>

            <div class="grid grid-cols-4 gap-6">
                <div class="col-span-4 md:col-span-3">
                    @if ($tags->count() > 0)
                        <div class="flex flex-wrap gap-3">
                            @foreach ($tags as $tag)
                                <div class="footer-anim"
                                    style="transition-delay: {{ number_format(min($loop->index, 12) * 0.05, 2) }}s;">
                                    <a href="{{ route('tags.show', $tag->slug) }}"
                                        class="card-shine group inline-flex items-center gap-2 px-4 py-2.5 rounded-xl overflow-hidden
                                               bg-white border border-gray-200 hover:border-teal-800
                                               hover:-translate-y-0.5 transition-transform duration-300">
                                        <img src="{{ asset('img/icons/hastag.webp') }}" class="w-4 opacity-40" alt="">
                                        <span class="text-sm font-medium text-gray-700 group-hover:text-teal-800 transition-colors">
                                            {{ Str::ucfirst($tag->name) }}
                                        </span>
                                        <span class="text-xs text-gray-400 group-hover:text-teal-600 transition-colors">
                                            ({{ $tag->pages()->count() }})
                                        </span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl bg-gray-200 border border-gray-300 p-10 text-center">
                            <p class="text-gray-400">{{ __('Hali hech qanday teg mavjud emas.') }}</p>
                        </div>
                    @endif
                </div>
                <x-main.sidebar />
            </div>
        </div>
    </div>
</x-main-layout>
