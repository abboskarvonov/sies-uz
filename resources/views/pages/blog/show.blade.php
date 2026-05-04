<x-main-layout
    :metaTitle="$metaTitle"
    :metaDescription="$metaDescription"
    :metaImage="$metaImage"
    :metaImageAlt="lc_title($page)"
    ogType="article">

    @php
        $settings = \App\Models\SiteSettings::instance();
        $articleSchema = json_encode([
            '@context'         => 'https://schema.org',
            '@type'            => 'NewsArticle',
            'headline'         => lc_title($page),
            'description'      => $metaDescription,
            'image'            => [$metaImage],
            'datePublished'    => $page->created_at?->toIso8601String(),
            'dateModified'     => $page->updated_at?->toIso8601String(),
            'author'           => [
                '@type' => 'Organization',
                'name'  => $settings->site_name_uz ?? 'SamISI',
                'url'   => url('/'),
            ],
            'publisher'        => [
                '@type' => 'Organization',
                'name'  => $settings->site_name_uz ?? 'SamISI',
                'logo'  => ['@type' => 'ImageObject', 'url' => $settings->logoUrl()],
            ],
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => url()->current()],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    @endphp
    @push('schema')
    <script type="application/ld+json">{!! $articleSchema !!}</script>
    @endpush

    <x-breadcrumb :menu="$menuModel" :submenu="$submenuModel ?? null" :multimenu="$multimenuModel ?? null" :page="$page ?? null" />

    {{-- ══ HEADER ══ --}}
    <x-page.show-header
        :title="lc_title($page)"
        :image="$page->imageUrl()"
        :date="$page->date?->format('Y-m-d')"
        :views="$page->views ?? 0"
        :activity="$page->activity ?? false"
    />

    {{-- ══ CONTENT ══ --}}
    <div class="bg-gray-100 px-4 lg:px-0 py-10" x-data
        x-intersect.once.threshold.10="$el.classList.add('footer-in')">
        <div class="container mx-auto">
            <div class="grid grid-cols-4 gap-6">

                {{-- Main content --}}
                <div class="col-span-4 md:col-span-3 flex flex-col min-w-0">
                    <div class="footer-anim rounded-2xl bg-white border border-gray-200 p-6 md:p-8 flex-1"
                        style="transition-delay: 0.10s;">

                        {{-- Prose --}}
                        <div class="prose max-w-none text-gray-700 text-justify indent-10">
                            {!! lc_content($page) !!}
                        </div>

                        @if ($page->activity)
                            <div class="mt-4 pt-4 border-t border-gray-200 flex flex-wrap items-center gap-2 text-sm text-gray-700">
                                <span class="font-medium">Onlayn translatsiya:</span>
                                <a href="https://sies.uz/uz/onlayn-translatsiya/onlayn-translatsiya/onlayn-himoya"
                                    class="text-teal-700 hover:underline break-all" target="_blank" rel="noopener noreferrer">
                                    https://sies.uz/uz/onlayn-translatsiya/onlayn-translatsiya/onlayn-himoya
                                </a>
                            </div>
                        @endif

                        {{-- Inline gallery --}}
                        @if (!empty($images) && is_array($images))
                            <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-2 lg:grid-cols-4">
                                @foreach ($images as $i => $url)
                                    <a data-fancybox="gallery" href="{{ $url }}" aria-label="Photo gallery"
                                        class="card-shine group relative block overflow-hidden rounded-xl
                                               border border-gray-200 hover:border-teal-800">
                                        <x-main.image src="{{ $url }}" :lazy="true"
                                            alt="Gallery image {{ $i + 1 }}"
                                            class="h-50 w-full object-cover transition duration-500 group-hover:scale-105" />
                                        <div
                                            class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition
                                                    flex items-center justify-center">
                                            <div
                                                class="w-9 h-9 rounded-full bg-white/20 backdrop-blur-sm border border-white/40
                                                        flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        {{-- File downloads --}}
                        @if ($page->files->isNotEmpty())
                            <div class="mt-6 pt-5 border-t border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                    {{ __('messages.files') ?? 'Fayllar' }}
                                </h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    @foreach ($page->files as $file)
                                        <a href="{{ asset('storage/' . $file->file) }}" download
                                            class="card-shine group flex flex-col items-center gap-2 rounded-xl p-4 overflow-hidden
                                                   bg-gray-100 border border-gray-200 hover:border-teal-800
                                                   text-center transition-colors">
                                            <img src="/img/icons/file.webp" alt=""
                                                class="w-8 opacity-60 group-hover:opacity-90 transition" />
                                            <span
                                                class="line-clamp-2 text-xs text-gray-600 group-hover:text-teal-800 transition-colors">
                                                {{ $file->name ?? basename($file->file) }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Sidebar --}}
                <x-main.sidebar />

            </div>
        </div>
    </div>

    @if (!empty($images) && is_array($images))
        @push('styles')
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
        @endpush
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Fancybox.bind("[data-fancybox='gallery']", {
                        Thumbs: {
                            autoStart: true
                        },
                        Toolbar: {
                            display: ["close"]
                        },
                    });
                });
            </script>
        @endpush
    @endif

</x-main-layout>
