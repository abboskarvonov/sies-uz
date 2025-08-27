{{-- resources/views/tags/index.blade.php --}}
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
                            <a href="{{ route('tags') }}"
                                class="ms-1 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white md:ms-2">
                                {{ __('Teglar') }}
                            </a>
                        </div>
                    </li>

                </ol>
            </nav>
        </div>
    </div>

    <div class="container mx-auto my-10 rounded-lg bg-gray-50 py-6 shadow dark:bg-gray-700 px-4">
        <h1 class="text-xl font-medium uppercase tracking-tight mb-6">
            {{ __('messages.tags') }}
        </h1>
        <div class="grid grid-cols-4 gap-4 py-5">
            <div class="col-span-4 md:col-span-3">
                @if ($tags->count() > 0)
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-6">
                        @foreach ($tags as $tag)
                            <a href="{{ route('tags.show', $tag->slug) }}"
                                class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                <img src="{{ asset('img/icons/hastag.webp') }}" class="w-4 inline-block dark:invert"
                                    alt="tag">
                                {{ Str::ucfirst($tag->name) }}
                                <span class="ml-1 text-sm text-gray-500">({{ $tag->pages()->count() }})</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600 dark:text-gray-300">{{ __('Hali hech qanday teg mavjud emas.') }}</p>
                @endif
            </div>
            <x-main.sidebar />
        </div>
    </div>
</x-main-layout>
