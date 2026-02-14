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
                                {{ lc_title($data) }}
                            </span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="dark:bg-gray-6 page-header relative mb-4 w-full bg-gray-200">
        <div class="absolute z-10 h-full w-full bg-white/75 dark:bg-gray-950/80"></div>
        <div class="container mx-auto relative z-20 grid grid-cols-5 gap-5 py-6 px-4 lg:px-0">
            <div class="col-span-5 grid place-content-center justify-start space-y-5 md:col-span-3">
                <h1 class="text-2xl font-medium uppercase tracking-tight">
                    {{ lc_title($data) }}
                </h1>

                <div class="flex items-center gap-2">
                    <div class="flex space-x-2">
                        <x-icon-button
                            onclick="window.open('https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode(lc_title($data)) }}','_blank')">
                            <img src="{{ asset('img/icons/telegram.webp') }}" class="w-5 h-5" alt="Telegram icon">
                        </x-icon-button>
                        <x-icon-button
                            onclick="window.open('https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}','_blank')">
                            <img src="{{ asset('img/icons/facebook.webp') }}" class="w-5 h-5" alt="Facebook icon">
                        </x-icon-button>
                    </div>
                    <x-icon-button onclick="copyToClipboard('{{ url()->current() }}')">
                        <img src="{{ asset('/img/icons/send.webp') }}" alt="Send img" class="w-4 dark:invert" />
                    </x-icon-button>
                </div>
            </div>
            <div class="col-span-5 md:col-span-2">
                <x-main.image src="{{ asset('storage/' . $data->image) }}" alt="{{ lc_title($data) }}"
                    class="max-h-[480px] w-full rounded-lg object-cover shadow" />
            </div>
        </div>
    </div>

    <div class="container mx-auto my-10 rounded-lg bg-gray-100 py-6 shadow dark:bg-gray-700">
        <div class="grid grid-cols-4 gap-4 px-4">
            <div class="col-span-4 rounded-xl bg-background md:col-span-3">
                <div class="rounded-xl bg-white dark:bg-gray-800 shadow p-4">
                    <div class="my-4 text-justify indent-10 prose max-w-none dark:prose-invert">
                        {!! lc_content($data) !!}
                    </div>
                </div>
            </div>
            <x-main.sidebar />
        </div>
    </div>
</x-main-layout>
