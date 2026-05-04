<x-main-layout :metaTitle="$metaTitle" :metaDescription="$metaDescription" :metaImage="$metaImage">

    {{-- Inline breadcrumb (no $menuModel hierarchy) --}}
    <div class="bg-teal-950 px-4 lg:px-0 py-10">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </li>
                <li>
                    <span
                        class="relative inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                                 text-white border border-teal-500/50 bg-teal-700/70 backdrop-blur-md"
                        style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.10);">
                        {{ lc_title($data) }}
                    </span>
                </li>
            </ol>
        </div>
    </div>

    {{-- ══ HEADER ══ --}}
    <x-page.show-header
        :title="lc_title($data)"
        :image="$data->imageUrl()"
    />

    {{-- ══ CONTENT ══ --}}
    <div class="bg-gray-100 px-4 lg:px-0 py-10" x-data x-intersect.once.threshold.10="$el.classList.add('footer-in')">
        <div class="container mx-auto">
            <div class="grid grid-cols-4 gap-6">
                <div class="col-span-4 md:col-span-3 flex flex-col min-w-0">
                    <div class="footer-anim rounded-2xl bg-white border border-gray-200 p-6 md:p-8 flex-1"
                        style="transition-delay: 0.10s;">
                        <div class="prose max-w-none text-gray-700 text-justify indent-10">
                            {!! lc_content($data) !!}
                        </div>
                    </div>
                </div>
                <x-main.sidebar />
            </div>
        </div>
    </div>
</x-main-layout>
