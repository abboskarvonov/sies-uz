<?php
$design_items = [
    // 1. Yangiliklar
    [
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>',
        'bg' => 'bg-teal-500/20',
        'text' => 'text-teal-300',
    ],
    // 2. Talabalar
    [
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>',
        'bg' => 'bg-cyan-500/20',
        'text' => 'text-cyan-300',
    ],
    // 3. Ta'lim/Kurslar
    [
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
        'bg' => 'bg-emerald-500/20',
        'text' => 'text-emerald-300',
    ],
    // 4. Ilmiy Faoliyat
    [
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>',
        'bg' => 'bg-teal-400/20',
        'text' => 'text-teal-200',
    ],
    // 5. E'lonlar
    [
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>',
        'bg' => 'bg-cyan-400/20',
        'text' => 'text-cyan-200',
    ],
    // 6. Kafedra/Bino
    [
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
        'bg' => 'bg-emerald-400/20',
        'text' => 'text-emerald-200',
    ],
];
$item_count = count($design_items);
$index = 0;
?>

<div class="w-full py-10 lg:py-20 px-4 lg:px-0" x-data x-intersect.once.threshold.10="$el.classList.add('footer-in')">
    <div class="container mx-auto">
        <h1
            class="flex items-center gap-3 text-xl font-medium uppercase md:text-2xl text-teal-800 mb-8 footer-anim footer-anim-d1">
            <span
                class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-700/10 border border-teal-700/20 shrink-0">
                <img src="/img/icons/hastag.webp" alt="Hash icon" class="w-5" />
            </span>
            {{ __('messages.tags') }}
        </h1>

        <div class="flex flex-wrap gap-3">
            @if ($tags)
                @foreach ($tags as $tag)
                    <?php
                    $design = $design_items[$index % $item_count];
                    $index++;
                    ?>

                    {{-- Outer: entrance animation --}}
                    <div class="footer-anim"
                        style="transition-delay: {{ number_format(min($loop->index, 10) * 0.06, 2) }}s;">

                        {{-- Inner <a>: hover + shine --}}
                        <a href="{{ route('tags.show', ['slug' => $tag->slug]) }}"
                            class="card-shine group flex items-center gap-3 rounded-xl px-4 py-3 overflow-hidden
                                  bg-teal-800 backdrop-blur-md
                                  border border-teal-700/40 hover:border-teal-400/60
                                  hover:bg-teal-700 hover:-translate-y-1 transition-transform duration-300"
                            style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);">

                            {{-- Icon container --}}
                            <div
                                class="shrink-0 w-10 h-10 flex items-center justify-center rounded-lg
                                        {{ $design['bg'] }} {{ $design['text'] }}
                                        group-hover:scale-110 transition-transform duration-300">
                                {!! $design['icon'] !!}
                            </div>

                            <span
                                class="text-base font-semibold text-teal-100 group-hover:text-white transition-colors whitespace-nowrap">
                                {{ $tag->name }}
                            </span>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
