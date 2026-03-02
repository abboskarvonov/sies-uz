@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}"
        class="flex items-center justify-center mt-6">

        {{-- Mobile: prev/next only --}}
        <div class="flex justify-between flex-1 sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium
                             text-teal-600 bg-teal-900/40 border border-teal-800/40 cursor-default">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('pagination.previous') }}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="card-shine inline-flex items-center gap-1.5 px-4 py-2 rounded-lg overflow-hidden
                           text-sm font-medium text-teal-200 hover:text-white
                           bg-teal-800 border border-teal-700/40 hover:border-teal-400/60
                           transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('pagination.previous') }}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="card-shine inline-flex items-center gap-1.5 px-4 py-2 rounded-lg overflow-hidden
                           text-sm font-medium text-teal-200 hover:text-white
                           bg-teal-800 border border-teal-700/40 hover:border-teal-400/60
                           transition-colors">
                    {{ __('pagination.next') }}
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium
                             text-teal-600 bg-teal-900/40 border border-teal-800/40 cursor-default">
                    {{ __('pagination.next') }}
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </span>
            @endif
        </div>

        {{-- Desktop: full pagination --}}
        <div class="hidden sm:flex items-center gap-1.5">

            {{-- Prev --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm
                             text-teal-600 bg-teal-900/40 border border-teal-800/40 cursor-default">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="card-shine inline-flex items-center justify-center w-9 h-9 rounded-lg overflow-hidden
                           text-teal-200 hover:text-white bg-teal-800 border border-teal-700/40 hover:border-teal-400/60
                           transition-colors"
                    aria-label="{{ __('pagination.previous') }}">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </a>
            @endif

            {{-- Page numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center px-2 h-9 text-sm text-teal-500 select-none">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                class="inline-flex items-center justify-center min-w-[36px] h-9 px-3 rounded-lg text-sm font-semibold
                                       text-white bg-teal-600/80 border border-teal-400/50 cursor-default"
                                style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.15);">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="card-shine inline-flex items-center justify-center min-w-[36px] h-9 px-3 rounded-lg overflow-hidden
                                       text-sm font-medium text-teal-200 hover:text-white
                                       bg-teal-800 border border-teal-700/40 hover:border-teal-400/60
                                       transition-colors"
                                aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="card-shine inline-flex items-center justify-center w-9 h-9 rounded-lg overflow-hidden
                           text-teal-200 hover:text-white bg-teal-800 border border-teal-700/40 hover:border-teal-400/60
                           transition-colors"
                    aria-label="{{ __('pagination.next') }}">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm
                             text-teal-600 bg-teal-900/40 border border-teal-800/40 cursor-default">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </span>
            @endif

        </div>
    </nav>
@endif
