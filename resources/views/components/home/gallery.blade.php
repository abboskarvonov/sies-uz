<div class="w-full py-10 lg:py-20 px-4 lg:px-0" x-data x-intersect.once.threshold.10="$el.classList.add('footer-in')">
    <div class="container mx-auto">
        <h1
            class="flex items-center gap-3 text-xl font-medium uppercase md:text-2xl text-teal-800 mb-8 footer-anim footer-anim-d1">
            <span
                class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-700/10 border border-teal-700/20 shrink-0">
                <img src="/img/icons/017-image.webp" alt="Book icon" class="w-5" />
            </span>
            {{ __('messages.gallery') }}
        </h1>

        <div class="gallery">
            @if ($galleryImages && $galleryImages->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    @foreach ($galleryImages as $image)
                        @if ($image)
                            <a data-fancybox="gallery" href="{{ $image }}" aria-label="Photo gallery"
                                class="card-shine footer-anim group relative block w-full pb-[100%] rounded-xl overflow-hidden
                                      border-2 border-teal-700/50 hover:border-teal-400/80
                                      transition-[border-color] duration-300"
                                style="transition-delay: {{ number_format(min($loop->index, 8) * 0.07, 2) }}s;">

                                <div class="absolute inset-0">
                                    @php
                                    // Mutlaq URL'dan nisbiy yo'l ajratish (srcset generatsiya uchun)
                                    $imgSrc = preg_replace('#^https?://[^/]+/#', '', $image);
                                @endphp
                                <x-main.image :src="$imgSrc" :alt="'Gallery'" :loading="'lazy'"
                                        sizes="(max-width: 640px) 50vw, (max-width: 1024px) 25vw, 17vw"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />

                                    <div
                                        class="absolute inset-0 bg-linear-to-t from-teal-950/80 via-teal-900/20 to-transparent
                                                opacity-0 group-hover:opacity-100 transition-opacity duration-300
                                                flex items-center justify-center">
                                        <div
                                            class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm border border-white/40
                                                    flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="text-center text-teal-600 py-10">{{ __('No images available') }}</p>
            @endif
        </div>
    </div>
</div>

@push('styles')
    {{-- Fancybox CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
@endpush

@push('scripts')
    {{-- Fancybox JS --}}
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Fancybox.bind("[data-fancybox='gallery']", {
                Thumbs: {
                    autoStart: true,
                },
                Toolbar: {
                    display: ["close"],
                },
            });
        });
    </script>
@endpush
