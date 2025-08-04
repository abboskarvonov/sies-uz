@php
    use Illuminate\Support\Str;
@endphp
<div class="py-10 lg:py-20 px-4">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl">
            <img src="{{ asset('img/icons/010-ssd.webp') }}" alt="Book icon" class="w-6 dark:invert" />
            News
        </h1>
        <div class="mt-5 flex flex-wrap justify-between md:mt-10">
            @php
                $locale = app()->getLocale();
                $titleField = 'title_' . $locale;
                $slugField = 'slug_' . $locale;
            @endphp
            @if ($latestNews)
                <div class="relative h-[550px] w-full overflow-hidden rounded-xl lg:w-2/5">
                    <div class="news-overlay z-10"></div>
                    <x-main.image class="h-full w-full object-cover" :lazy="false"
                        src="{{ asset('storage/' . $latestNews->image) }}" width="600" height="400" />
                    <div class="absolute bottom-14 left-0 z-20 grid w-full px-10 py-5 text-white">
                        <a href="{{ $latestNews->$slugField }}" class="text-lg">
                            {{ $latestNews->$titleField }}
                        </a>
                        <div class="mt-2 flex gap-10 text-sm">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('/img/icons/011-clock.webp') }}" alt="Book icon"
                                    class="w-3 invert" />
                                {{ $latestNews->date?->format('Y-m-d') ?? 'No date' }}
                            </div>
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('/img/icons/012-user.webp') }}" alt="Book icon" class="w-3 invert" />
                                {{ $latestNews->views ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-5 hidden h-[550px] content-between md:grid md:w-2/4 lg:mt-0 lg:w-1/4">
                @foreach ($otherNews as $news)
                    <div class="relative h-[172px] w-full overflow-hidden rounded-xl">
                        <x-main.image class="h-full w-full object-cover" src="{{ asset('storage/' . $news->image) }}" />
                        <div class="absolute bottom-2 z-20 w-full p-4 text-sm text-white">
                            <a href="{{ $news->{'slug_' . app()->getLocale()} }} }}">
                                {{ Str::limit($news->{'title_' . app()->getLocale()}, 100) }}
                            </a>
                        </div>
                        <div class="news-small-overlay"></div>
                    </div>
                @endforeach

            </div>

            <div
                class="mt-5 grid h-[550px] w-full content-between overflow-hidden rounded-xl bg-gray-200 p-4 shadow-inner dark:bg-gray-800 md:w-5/12 lg:mt-0 lg:w-1/3">
                <div class="flex items-center gap-4 border-b-2 border-b-foreground pb-3">
                    <img src="{{ asset('img/icons/ann.webp') }}" alt="SamISI" class="w-8 dark:invert" />
                    <p class="text-2xl font-bold">
                        Advertisements
                    </p>
                </div>
                @foreach ($announcements as $item)
                    <div
                        class="h-[140px] overflow-hidden rounded-lg border border-solid border-gray-400 bg-gray-50 shadow-lg dark:border-gray-400 dark:bg-gray-500">
                        <div class="flex justify-between">
                            <div class="w-3/5 ps-3 pt-3 text-sm">
                                <a href="{{ $item->{'slug_' . app()->getLocale()} }}">
                                    {{ Str::limit($item->{'title_' . app()->getLocale()}, 100) }}
                                </a>
                                <div class="flex gap-8 pt-2">
                                    <div class="flex items-center gap-2">
                                        <img src="/img/icons/011-clock.webp" alt="Book icon" class="w-3 dark:invert" />
                                        {{ $item->date?->format('Y-m-d') ?? 'No date' }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <img src="/img/icons/012-user.webp" alt="Book icon" class="w-3 dark:invert" />
                                        {{ $item->views ?? 0 }}
                                    </div>
                                </div>
                            </div>
                            <div class="relative h-[140px] w-2/5">
                                <img class="h-full w-full object-cover" src="{{ asset('storage/' . $item->image) }}" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
