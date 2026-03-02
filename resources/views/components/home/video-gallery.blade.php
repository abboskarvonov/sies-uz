@php
    $videos = [
        ['id' => 'yauUumiDtf8', 'large' => true],
        ['id' => 'GJ48GmaZHZc'],
        ['id' => 'y04WHmW2w4k'],
        ['id' => 'usksLpZzEbk'],
        ['id' => '4vknIp5pPK8'],
    ];
@endphp

<div class="w-full py-10 lg:py-20 px-4 lg:px-0 bg-gray-100" x-data
    x-intersect.once.threshold.10="$el.classList.add('footer-in')">
    <div class="container mx-auto">
        <h1
            class="flex items-center gap-3 text-xl font-medium uppercase md:text-2xl text-teal-800 mb-8 footer-anim footer-anim-d1">
            <span
                class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-500/20 border border-teal-500/40 shrink-0">
                <img src="{{ asset('/img/icons/youtube-logo.webp') }}" alt="Video icon" class="w-5" width="20"
                    height="20" />
            </span>
            {{ __('messages.video') }}
        </h1>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
            @foreach ($videos as $video)
                @php $isLarge = !empty($video['large']); @endphp

                {{-- Outer: entrance animation --}}
                <div class="footer-anim {{ $isLarge ? 'col-span-2 row-span-2' : '' }}"
                    style="transition-delay: {{ number_format($loop->index * 0.12, 2) }}s;">

                    {{-- Card shell: shine + border --}}
                    <div class="card-shine group relative w-full overflow-hidden rounded-2xl
                                border border-teal-700/50 hover:border-teal-400/70
                                {{ $isLarge ? 'h-56 md:h-105' : 'h-28 md:h-50' }}"
                        style="box-shadow: 0 4px 24px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.06);">

                        {{-- Lite YouTube player --}}
                        <div class="lite-youtube w-full h-full cursor-pointer" data-video-id="{{ $video['id'] }}"
                            role="button" tabindex="0" aria-label="YouTube videoni ijro etish">

                            {{-- Thumbnail --}}
                            <img src="https://i.ytimg.com/vi/{{ $video['id'] }}/{{ $isLarge ? 'maxresdefault' : 'hqdefault' }}.jpg"
                                alt="SamISI YouTube video"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                loading="lazy" width="{{ $isLarge ? 1280 : 480 }}"
                                height="{{ $isLarge ? 720 : 360 }}" />

                            {{-- Teal gradient overlay --}}
                            <div
                                class="absolute inset-0 bg-linear-to-t from-teal-950/70 via-transparent to-transparent
                                        group-hover:from-teal-950/50 transition-all duration-300">
                            </div>

                            {{-- Play button --}}
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div
                                    class="flex items-center justify-center rounded-full
                                            bg-white/15 backdrop-blur-sm border border-white/30
                                            group-hover:bg-white/25 group-hover:border-white/50 group-hover:scale-110
                                            transition-all duration-300
                                            {{ $isLarge ? 'w-16 h-16 md:w-20 md:h-20' : 'w-10 h-10 md:w-12 md:h-12' }}">
                                    {{-- YouTube red play icon --}}
                                    <svg class="{{ $isLarge ? 'w-8 h-8 md:w-10 md:h-10' : 'w-5 h-5 md:w-6 md:h-6' }}
                                               drop-shadow-lg"
                                        viewBox="0 0 68 48">
                                        <path
                                            d="M66.52 7.74c-.78-2.93-2.49-5.41-5.42-6.19C55.79.13 34 0 34 0S12.21.13 6.9 1.55C3.97 2.33 2.27 4.81 1.48 7.74.06 13.05 0 24 0 24s.06 10.95 1.48 16.26c.78 2.93 2.49 5.41 5.42 6.19C12.21 47.87 34 48 34 48s21.79-.13 27.1-1.55c2.93-.78 4.64-3.26 5.42-6.19C67.94 34.95 68 24 68 24s-.06-10.95-1.48-16.26z"
                                            fill="#FF0000" />
                                        <path d="M45 24 27 14v20" fill="white" />
                                    </svg>
                                </div>
                            </div>

                            {{-- YouTube label (large card only) --}}
                            @if ($isLarge)
                                <div class="absolute bottom-4 left-4 flex items-center gap-2 opacity-80">
                                    <img src="{{ asset('/img/icons/youtube.webp') }}" alt="" class="w-4 h-4" />
                                    <span class="text-xs text-white font-medium">YouTube</span>
                                </div>
                            @endif
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
                iframe.allow = 'autoplay; encrypted-media; fullscreen';
                iframe.allowFullscreen = true;
                // Security fix (CWE-829): sandbox restricts YouTube iframe to needed capabilities only
                iframe.setAttribute('sandbox',
                    'allow-scripts allow-same-origin allow-presentation allow-popups');
                this.replaceWith(iframe);
            });
        });
    </script>
@endpush
