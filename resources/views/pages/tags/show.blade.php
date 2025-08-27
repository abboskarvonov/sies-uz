<x-main-layout :metaTitle="$metaTitle" :metaDescription="$metaDescription" :metaImage="$metaImage">
    <div class="bg-gray-100 dark:bg-gray-950 px-4 lg:px-0">
        <div class="container mx-auto py-10">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    {{-- Home --}}
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}"
                            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                            <img alt="Home" src="{{ asset('img/icons/home.webp') }}" class="w-4 dark:invert">
                        </a>
                    </li>

                    {{-- Menu --}}
                    <li>
                        <div class="flex items-center">
                            <span class="text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg> {{-- SVG chevron --}}
                            </span>
                            <a href="{{ route('tags') }}"
                                class="ms-1 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white md:ms-2">
                                {{ __('messages.tags') }}
                            </a>
                        </div>
                    </li>

                    <li>
                        <div class="flex items-center">
                            <span class="text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                            <span
                                class="ms-1 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white md:ms-2">
                                {{ ucfirst($tag->name) }}
                            </span>
                        </div>
                    </li>



                </ol>
            </nav>
        </div>
    </div>

    <div class="container mx-auto my-10 rounded-lg bg-gray-50 py-6 shadow dark:bg-gray-700 px-4">
        <h1 class="text-xl font-semibold tracking-tight">
            #{{ $tag->name }} {{ __('messages.tag_text') }}
        </h1>
        <div class="grid grid-cols-4 gap-4 py-5">
            <div class="col-span-4 md:col-span-3">
                @if ($pages->count())
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($pages as $page)
                            <div class="rounded-lg shadow bg-white dark:bg-gray-800 p-2">
                                <a
                                    href="{{ localized_page_route($page->menu, $page->submenu, $page->multimenu, $page) }}">
                                    <x-main.image class="h-60 w-full rounded-t-md object-cover"
                                        src="{{ asset('storage/' . $page->image) }}" />
                                </a>
                                <div class="p-2 pt-5">
                                    <div class="flex gap-6 text-sm">
                                        <div class="flex items-center gap-1">
                                            <img src="/img/icons/011-clock.webp" alt="Book icon"
                                                class="w-3 dark:invert" />
                                            {{ $page->date?->format('Y-m-d') }}
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <img src="/img/icons/012-user.webp" alt="Book icon"
                                                class="w-3 dark:invert" />
                                            {{ $page->views ?? 0 }}
                                        </div>
                                    </div>
                                    <a
                                        href="{{ localized_page_route($page->menu, $page->submenu, $page->multimenu, $page) }}">
                                        <h4 class="my-2 font-bold tracking-tighter text-gray-900 dark:text-white">
                                            {{ Str::limit(lc_title($page), 44) }}
                                        </h4>
                                    </a>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ Str::limit(strip_tags(lc_content($page)), 140) }}
                                    </p>
                                    <x-button class="mt-3">
                                        <a href="{{ localized_page_route($page->menu, $page->submenu, $page->multimenu, $page) }}"
                                            class="flex items-center gap-2">
                                            {{ __('messages.read_more') }}
                                            <svg class="h-3.5 w-3.5 rtl:rotate-180" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M1 5h12m0 0L9 1m4 4L9 9" />
                                            </svg>
                                        </a>
                                    </x-button>
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-6 flex justify-center">
                            {{ $pages->onEachSide(1)->links() }}
                        </div>
                    </div>
                @else
                    <div class="mt-6 rounded-md bg-white p-6 text-center dark:bg-gray-800">
                        {{ __('Hozircha ushbu tegga biriktirilgan sahifalar yo‘q.') }}
                    </div>
                @endif
            </div>
            <x-main.sidebar />
        </div>

    </div>
</x-main-layout>
