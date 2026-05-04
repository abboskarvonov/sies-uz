@props(['title', 'image', 'date' => null, 'views' => null, 'subtitle' => null, 'activity' => false])

<div class="relative bg-cover bg-center px-4 lg:px-0 py-10" style="background-image: url('/img/hero-bg-1920.webp');">
    <div class="absolute inset-0 bg-teal-950/40"></div>
    <div class="relative container mx-auto">
        <div class="grid lg:grid-cols-5 gap-8 items-start">

            {{-- Left: info card --}}
            <div class="lg:col-span-3 rounded-2xl p-6 md:p-8 bg-teal-800/70 backdrop-blur-md border border-teal-700/40
                        page-anim-left page-anim-d1"
                style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);">

                @if ($subtitle)
                    {{-- Staff layout --}}
                    <div class="flex items-center gap-3 mb-3 page-anim-up page-anim-d2">
                        <img src="{{ asset('img/icons/user-tie.webp') }}" class="w-6 h-6" alt="">
                        <h1 class="md:text-xl font-bold text-white leading-tight">{{ $title }}</h1>
                    </div>
                    <div class="flex items-center gap-2 mb-6 page-anim-up page-anim-d3">
                        <img src="{{ asset('img/icons/checklist.webp') }}" class="w-6 h-6" alt="">
                        <p class="text-teal-300 text-sm">{{ $subtitle }}</p>
                    </div>
                @else
                    {{-- Default layout --}}
                    <h1 class="md:text-xl font-bold text-white leading-tight mb-5 page-anim-up page-anim-d2">{{ $title }}</h1>
                    @if ($date !== null || $views !== null)
                        <div class="flex items-center gap-5 text-sm text-teal-300 mb-6 page-anim-up page-anim-d3">
                            @if ($date !== null)
                                <span class="flex gap-1.5 items-center">
                                    <img src="/img/icons/011-clock.webp" alt=""
                                        class="w-3.5 invert opacity-70" />
                                    {{ $date }}
                                </span>
                            @endif
                            @if ($views !== null)
                                <span class="flex gap-1.5 items-center">
                                    <img src="/img/icons/012-user.webp" alt=""
                                        class="w-3.5 invert opacity-70" />
                                    {{ $views }}
                                </span>
                            @endif
                        </div>
                    @endif
                @endif

                {{-- Separator --}}
                <div class="h-px mb-6 page-anim-fade page-anim-d3"
                    style="background: linear-gradient(90deg, rgba(20,184,166,0.4), rgba(255,255,255,0.06), transparent);">
                </div>

                {{-- Share buttons --}}
                <div class="flex items-center gap-2 flex-wrap page-anim-up page-anim-d4">
                    <span class="text-xs text-teal-400 uppercase tracking-wider mr-1">Share:</span>
                    <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($title) }}"
                        target="_blank" rel="noopener"
                        class="card-shine inline-flex items-center gap-2 px-3 py-2 rounded-lg overflow-hidden
                               bg-teal-700/60 border border-teal-600/40 hover:border-teal-400/60
                               text-sm text-teal-100 hover:text-white transition-colors">
                        <img src="{{ asset('img/icons/telegram.webp') }}" class="w-4 h-4" alt="Telegram" /> Telegram
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                        target="_blank" rel="noopener"
                        class="card-shine inline-flex items-center gap-2 px-3 py-2 rounded-lg overflow-hidden
                               bg-teal-700/60 border border-teal-600/40 hover:border-teal-400/60
                               text-sm text-teal-100 hover:text-white transition-colors">
                        <img src="{{ asset('img/icons/facebook.webp') }}" class="w-4 h-4" alt="Facebook" /> Facebook
                    </a>
                    <button onclick="copyToClipboard('{{ url()->current() }}')"
                        class="card-shine inline-flex items-center gap-2 px-3 py-2 rounded-lg overflow-hidden
                               bg-teal-700/60 border border-teal-600/40 hover:border-teal-400/60
                               text-sm text-teal-100 hover:text-white transition-colors">
                        <img src="{{ asset('/img/icons/send.webp') }}" alt=""
                            class="w-3.5 invert opacity-80" />
                        {{ __('messages.copy_link') ?? 'Copy' }}
                    </button>
                </div>
            </div>

            {{-- Right: image --}}
            <div class="lg:col-span-2 page-anim-right page-anim-d2">
                <x-main.image :src="$image" :alt="$title"
                    class="w-full rounded-2xl object-cover border border-teal-700/40 {{ $activity ? 'max-h-[520px]' : 'max-h-95' }}"
                    style="box-shadow: 0 8px 32px rgba(0,0,0,0.4);" />
            </div>

        </div>
    </div>
</div>
