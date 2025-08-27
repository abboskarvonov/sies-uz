<div class="w-full bg-gray-50 py-10 dark:bg-gray-700 lg:py-20 px-4 lg:px-0">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl">
            <img src="/img/icons/016-bookmark.webp" alt="Book icon" class="w-6 dark:invert" />
            {{ __('messages.departments') }}
        </h1>
        <div class="mt-3 grid grid-cols-1 gap-2 pb-10 md:grid-cols-3 lg:mt-10 lg:grid-cols-6">
            @if ($departments)
                @foreach ($departments as $department)
                    <div class="relative h-[300px] overflow-hidden rounded-md shadow lg:h-[350px]">
                        <x-main.image class="block h-full w-full object-cover"
                            src="{{ asset('storage/' . $department->image) }}" alt="{{ lc_title($department) }}" />
                        <div class="slider-overlay absolute top-0 z-20 h-full w-full">
                            <h1 class="absolute bottom-10 left-0 w-full bg-gray-800 p-2 text-center text-white">
                                <a href="/uz/pages-view/iqtisodiyot-kafedrasi">
                                    {{ lc_title($department) }}
                                </a>
                            </h1>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="mx-auto grid w-52">
            <x-button>
                <a href="/uz/pages-view/kafedralar">
                    {{ __('messages.all_departments') }}
                </a>
            </x-button>
        </div>
    </div>
</div>
