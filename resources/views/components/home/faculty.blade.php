<div class="w-full bg-gray-50 py-10 dark:bg-gray-700 lg:py-20 px-4 lg:px-0">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl">
            <img src="/img/icons/014-graduation-hat.webp" alt="Book icon" class="w-7 dark:invert" />
            Fakultetlar
        </h1>
        <div class="mt-3 grid grid-cols-1 gap-10 pb-10 md:grid-cols-2 lg:mt-10 lg:grid-cols-4">
            @if ($faculties)
                @foreach ($faculties as $faculty)
                    <div class="relative h-[450px] overflow-hidden rounded-md shadow lg:h-[550px]">
                        <x-main.image class="block h-full w-full object-cover"
                            src="{{ asset('storage/' . $faculty->image) }}"
                            alt="{{ $faculty->{'title_' . app()->getLocale()} }}" />
                        <div class="slider-overlay absolute top-0 z-20 h-full w-full">
                            <h1 class="absolute bottom-10 left-0 w-full bg-gray-800 px-2 py-6 text-center text-white">
                                <a href="/uz/pages-view/iqtisodiyot-fakulteti">
                                    {{ $faculty->{'title_' . app()->getLocale()} }}
                                </a>
                            </h1>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="mx-auto grid w-52">
            <x-button>
                <a href="/uz/pages-view/fakultetlar">
                    Barcha fakultetlar
                </a>
            </x-button>
        </div>
    </div>
</div>
