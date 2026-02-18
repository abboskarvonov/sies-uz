@php
    $videos = [
        ['id' => 'yauUumiDtf8', 'large' => true],
        ['id' => 'GJ48GmaZHZc'],
        ['id' => 'y04WHmW2w4k'],
        ['id' => 'usksLpZzEbk'],
        ['id' => '4vknIp5pPK8'],
    ];
@endphp

<div class="w-full pt-10 lg:pt-20 pb-10 lg:pb-40 px-4 lg:px-0 bg-gray-50 py-10 dark:bg-gray-700 ">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl">
            <img src="{{ asset('/img/icons/youtube.webp') }}" alt="Video icon" class="w-6 dark:invert" width="24" height="24" />
            {{ __('messages.video') }}
        </h1>
        <div class="grid grid-cols-2 md:grid-cols-4 mt-5 md:mt-10 gap-2 md:gap-6 lg:gap-10">
            @foreach ($videos as $video)
                <div class="overflow-hidden rounded-xl {{ !empty($video['large']) ? 'h-60 md:h-96 row-span-2 col-span-2' : '' }}">
                    <div class="lite-youtube relative w-full h-full cursor-pointer group bg-black"
                         data-video-id="{{ $video['id'] }}"
                         role="button"
                         tabindex="0"
                         aria-label="YouTube videoni ijro etish">
                        <img src="https://i.ytimg.com/vi/{{ $video['id'] }}/hqdefault.jpg"
                             alt="SamISI YouTube video"
                             class="w-full h-full object-cover group-hover:opacity-80 transition-opacity"
                             loading="lazy"
                             width="480" height="360" />
                        {{-- Play button --}}
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-16 h-16 text-red-600 drop-shadow-lg group-hover:scale-110 transition-transform" viewBox="0 0 68 48">
                                <path d="M66.52 7.74c-.78-2.93-2.49-5.41-5.42-6.19C55.79.13 34 0 34 0S12.21.13 6.9 1.55C3.97 2.33 2.27 4.81 1.48 7.74.06 13.05 0 24 0 24s.06 10.95 1.48 16.26c.78 2.93 2.49 5.41 5.42 6.19C12.21 47.87 34 48 34 48s21.79-.13 27.1-1.55c2.93-.78 4.64-3.26 5.42-6.19C67.94 34.95 68 24 68 24s-.06-10.95-1.48-16.26z" fill="red"/>
                                <path d="M45 24 27 14v20" fill="white"/>
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.lite-youtube').forEach(el => {
    el.addEventListener('click', function() {
        const id = this.dataset.videoId;
        const iframe = document.createElement('iframe');
        iframe.width = '100%';
        iframe.height = '100%';
        iframe.src = 'https://www.youtube.com/embed/' + id + '?autoplay=1';
        iframe.allow = 'autoplay; encrypted-media';
        iframe.allowFullscreen = true;
        this.replaceWith(iframe);
    });
});
</script>
@endpush
