<div class="w-full bg-gray-50 py-10 dark:bg-gray-700 lg:py-20 px-4 lg:px-0">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl">
            <img src="/img/icons/016-bookmark.webp" alt="Book icon" class="w-6 dark:invert" />
            {{ __('messages.departments') }}
        </h1>
        <div class="mt-3 grid grid-cols-1 gap-4 pb-10 md:grid-cols-2 lg:mt-10 lg:grid-cols-3">
            @if ($departments)
                @foreach ($departments as $department)
                    <a href="{{ localized_page_route($department->menu, $department->submenu, $department->multimenu, $department) }}"
                        class="bg-white dark:bg-gray-800 rounded-lg p-4 flex items-start space-x-4 border-l-4 border-gray-600 dark:border-gray-400 transition-shadow duration-300 hover:shadow-xl dark:hover:shadow-gray-600">
                        <div
                            class="flex-shrink-0 w-36 h-36 rounded-full overflow-hidden border-2 border-gray-200 dark:border-gray-400">
                            <x-main.image class="w-full h-full object-cover"
                                src="{{ asset('storage/' . $department->image) }}" alt="{{ lc_title($department) }}" />
                        </div>
                        <div class="flex-grow">
                            <h3
                                class="text-xl font-bold text-gray-900 leading-snug hover:text-gray-700 dark:text-gray-100">
                                {{ lc_title($department) }}
                            </h3>
                            <p class="text-sm text-justify text-gray-500 dark:text-gray-300 line-clamp-6 mt-1">
                                {{ Str::limit(strip_tags(lc_content($department)), 280) }}</p>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>
        @if($departments->isNotEmpty())
            <div class="mx-auto grid w-52">
                <x-button>
                    <a
                        href="{{ localized_page_route($departments->first()->menu, $departments->first()->submenu, $departments->first()->multimenu) }}">
                        {{ __('messages.all_departments') }}
                    </a>
                </x-button>
            </div>
        @endif
    </div>
</div>
