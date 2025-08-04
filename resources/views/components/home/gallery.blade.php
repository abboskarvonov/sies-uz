<div class="w-full py-10 lg:py-20 px-4 lg:px-0">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl">
            <img src="/img/icons/017-image.webp" alt="Book icon" class="w-6 dark:invert" />
            Institutimiz hayotidan foto lavhalar
        </h1>
        <div class="gallery mt-10">

            <div class="grid grid-cols-2 gap-1 lg:grid-cols-4">
                @if ($galleryImages)
                    @foreach ($galleryImages as $image)
                        <a data-fancybox="gallery" href="{{ $image }}" aria-label="Photo gallery">
                            <img src=
                                "{{ $image }}"
                                class="h-[250px] w-full overflow-hidden rounded-lg object-cover" />
                        </a>
                    @endforeach
                @endif
            </div>
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
