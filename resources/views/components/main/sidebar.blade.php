<div class="col-span-4 mt-6 space-y-4 md:col-span-1 md:mt-0">

    {{-- Yil uchun banner --}}
    <div class="footer-anim rounded-2xl overflow-hidden border border-teal-700/30 relative shadow-sm"
        style="transition-delay: 0.15s;">
        <div class="relative">
            <img src="{{ asset('img/year.webp') }}" alt="SamISI" class="w-full h-56 object-cover block" />
            {{-- Gradient overlay --}}
            <div class="absolute inset-0 bg-linear-to-t from-teal-950 via-teal-900/40 to-transparent"></div>
            {{-- Text block --}}
            <div class="absolute bottom-0 left-0 right-0 px-4 pb-4 text-center">
                {{-- Dekorative divider --}}
                <div class="flex items-center justify-center gap-2 mb-2.5">
                    <div class="h-px flex-1 bg-teal-400/30"></div>
                    <svg class="w-3.5 h-3.5 text-yellow-400 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                    <div class="h-px flex-1 bg-teal-400/30"></div>
                </div>
                <p class="font-medium text-white/90 uppercase leading-snug tracking-wide">
                    {{ __('messages.year_name') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Menyular --}}
    <div class="footer-anim rounded-2xl border border-gray-200 bg-white overflow-hidden"
        style="transition-delay: 0.30s;">
        <h2 class="px-4 py-3 text-sm font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">
            {{ __('messages.menu') }}
        </h2>
        <div class="p-2 space-y-0.5">
            @foreach ($menus as $menu)
                <a href="{{ $menu->link ?: localized_page_route($menu) }}"
                    @if($menu->link) target="_blank" rel="noopener noreferrer" @endif
                    class="flex items-center gap-2 w-full rounded-lg px-3 py-2
                           text-sm text-gray-600 hover:text-teal-800 hover:bg-white
                           border border-transparent hover:border-teal-800/20 transition-colors">
                    <span class="w-1 h-1 rounded-full bg-teal-600 shrink-0"></span>
                    {{ $menu->{'title_' . app()->getLocale()} }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Teglar --}}
    <div class="footer-anim rounded-2xl border border-gray-200 bg-white overflow-hidden"
        style="transition-delay: 0.45s;">
        <h2 class="px-4 py-3 text-sm font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-200">
            {{ __('messages.tags') }}
        </h2>
        <div class="p-3 flex flex-wrap gap-2">
            @foreach ($tags as $tag)
                <a href="{{ route('tags.show', ['slug' => $tag->slug]) }}"
                    class="card-shine flex items-center gap-1 rounded-lg px-2.5 py-1 overflow-hidden
                           text-xs text-gray-600 hover:text-teal-800
                           border border-gray-200 hover:border-teal-800
                           bg-gray-100 hover:bg-teal-50 transition-colors">
                    <img src="{{ asset('img/icons/hastag.webp') }}" alt="" class="w-3 opacity-40" />
                    {{ $tag->name }}
                </a>
            @endforeach
        </div>
    </div>

</div>
