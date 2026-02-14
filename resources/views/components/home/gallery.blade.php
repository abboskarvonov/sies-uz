<div class="w-full py-10 lg:py-20 px-4 lg:px-0">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl">
            <img src="/img/icons/017-image.webp" alt="Book icon" class="w-6 dark:invert" />
            {{ __('messages.gallery') }}
        </h1>

        <div class="gallery mt-10">
            @if ($galleryImages && $galleryImages->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
                    @foreach ($galleryImages as $image)
                        @if($image)
                            <a data-fancybox="gallery" href="{{ $image }}" aria-label="Photo gallery"
                                class="group relative block w-full pb-[100%] rounded-xl overflow-hidden shadow-md transition-shadow duration-300 hover:shadow-xl">

                                <div class="absolute inset-0">
                                    <x-main.image :src="$image" :alt="$image" :loading="'lazy'"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />

                                    <div
                                        class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                        <span class="text-white text-2xl font-semibold">+</span>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 py-10">{{ __('No images available') }}</p>
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
