<x-main-layout>
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
                            <a href="#"
                                class="ms-1 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white md:ms-2">
                                {{ __('messages.search') }}
                            </a>
                        </div>
                    </li>

                </ol>
            </nav>
        </div>
    </div>
    <div class="container mx-auto my-10 rounded-lg bg-gray-50 py-6 shadow dark:bg-gray-700 px-4">
        <h1 class="text-2xl font-bold mb-4">Qidiruv natijalari: "{{ $query }}"</h1>

        @if ($results && $results->isEmpty())
            <p>Hech qanday natija topilmadi.</p>
        @elseif($results)
            <div class="grid grid-cols-4 gap-4 py-5">
                <div class="col-span-4 md:col-span-3">
                    <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- Har bir natija uchun --}}
                        @foreach ($results as $page)
                            <li
                                class="p-4 border rounded hover:bg-gray-100 bg-white dark:bg-gray-800 dark:hover:bg-gray-900">
                                <a
                                    href="{{ localized_page_route($page->menu, $page->submenu, $page->multimenu, $page) }}">
                                    <h2 class="text-lg font-semibold">
                                        {{ $page['title_' . app()->getLocale()] }}
                                    </h2>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-6">
                        {{ $results->links() }}
                    </div>
                </div>
                <x-main.sidebar />
            </div>
        @endif
    </div>
</x-main-layout>
