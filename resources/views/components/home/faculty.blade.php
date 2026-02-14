<div class="w-full bg-gray-50 py-10 dark:bg-gray-700 lg:py-20 px-4 lg:px-0">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl">
            <img src="/img/icons/014-graduation-hat.webp" alt="Book icon" class="w-7 dark:invert" />
            {{ __('messages.faculty') }}
        </h1>

        <section class="mt-3 grid grid-cols-1 gap-10 pb-10 md:grid-cols-2 lg:mt-10 lg:grid-cols-4">
            @if ($faculties)
                @foreach ($faculties as $faculty)
                    <a href="{{ localized_page_route($faculty->menu, $faculty->submenu, $faculty->multimenu, $faculty) }}"
                        class="block bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border-b-4 border-gray-600 dark:border-gray-400 transition-shadow duration-300 hover:shadow-xl">
                        <x-main.image class="w-full h-96 object-cover" src="{{ asset('storage/' . $faculty->image) }}"
                            alt="{{ lc_title($faculty) }}" />
                        <div class="p-4">
                            <h3
                                class="text-xl text-center font-bold text-gray-900 dark:text-gray-100 mb-1 line-clamp-2">
                                {{ lc_title($faculty) }}</h3>
                        </div>
                    </a>
                @endforeach
            @endif
        </section>
        @if($faculties->isNotEmpty())
            <div class="mx-auto grid w-52">
                <x-button>
                    <a
                        href="{{ localized_page_route($faculties->first()->menu, $faculties->first()->submenu, $faculties->first()->multimenu) }}">
                        {{ __('messages.all_faculties') }}
                    </a>
                </x-button>
            </div>
        @endif
    </div>
</div>
