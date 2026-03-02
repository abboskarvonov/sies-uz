@if ($quickLinks && $quickLinks->count() > 0)
    <div class="bg-linear-to-r from-teal-700 to-teal-800 dark:from-teal-800 dark:to-teal-900 py-3 px-2">
        <div class="container mx-auto">
            <div class="flex flex-wrap items-center justify-end gap-2">
                <!-- Quick Links Label -->
                {{-- <div class="hidden md:flex items-center gap-2 mr-4">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="text-white font-semibold text-sm uppercase">
                        {{ __('messages.quick_links') }}
                    </span>
                </div> --}}

                <!-- Quick Links Menu Items -->
                <div class="flex flex-wrap items-center justify-end gap-2">
                    @foreach ($quickLinks as $link)
                        <a href="{{ $link->link ?: '#' }}" target="{{ $link->link ? '_blank' : '_self' }}"
                            class="card-shine group flex items-center gap-2 px-4 py-2 overflow-hidden bg-white/10 hover:bg-white/20 dark:bg-gray-800/50 dark:hover:bg-gray-700/70 text-white rounded-lg transition-all duration-300 transform border border-white/20 dark:border-gray-600 header-btn-anim"
                            style="animation-delay: {{ 400 + $loop->index * 70 }}ms">
                            @if ($link->image)
                                <img src="{{ asset('storage/' . $link->image) }}"
                                    alt="{{ $link->{'title_' . app()->getLocale()} }}"
                                    class="w-4 h-4 opacity-90 group-hover:opacity-100 transition-opacity" />
                            @endif
                            <span class="text-xs font-medium uppercase tracking-wide">
                                {{ $link->{'title_' . app()->getLocale()} }}
                            </span>
                            @if ($link->link)
                                <svg class="w-3 h-3 opacity-70 group-hover:opacity-100 transition-opacity"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
