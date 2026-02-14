<x-main-layout :metaTitle="$metaTitle" :metaDescription="$metaDescription" :metaImage="$metaImage">
    <x-breadcrumb :menu="$menuModel" :submenu="$submenuModel ?? null" :multimenu="$multimenuModel ?? null" :page="$page ?? null" />
    <div class="dark:bg-gray-6 page-header relative mb-4 w-full bg-gray-200">
        <div class="absolute z-10 h-full w-full bg-white/75 dark:bg-gray-950/80"></div>
        <div class="container mx-auto relative z-20 grid grid-cols-5 gap-5 py-6 px-4 lg:px-0">
            <div class="col-span-5 grid place-content-center justify-start space-y-5 md:col-span-3">
                <h1 class="text-2xl font-medium uppercase tracking-tight">
                    {{ lc_title($page) }}
                </h1>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-1">
                        <img src="/img/icons/011-clock.webp" alt="Book icon" class="w-3 dark:invert" />
                        {{ $page->date?->format('Y-m-d') }}
                    </div>
                    <div class="flex items-center gap-1">
                        <img src="/img/icons/012-user.webp" alt="Book icon" class="w-3 dark:invert" />
                        {{ $page->views ?? 0 }}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex space-x-2">
                        <x-icon-button
                            onclick="window.open('https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode(lc_title($page)) }}','_blank')">
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
                <x-main.image src="{{ asset('storage/' . $page->image) }}" alt="{{ lc_title($page) }}"
                    class="max-h-[480px] w-full rounded-lg object-cover shadow" />
            </div>
        </div>
    </div>
    <div class="container mx-auto my-10 rounded-lg bg-gray-100 py-6 shadow dark:bg-gray-700">
        <div class="grid grid-cols-4 gap-4 px-4">
            <div class="col-span-4 rounded-xl bg-background md:col-span-3">
                <div class="rounded-xl bg-white dark:bg-gray-800 shadow p-4">
                    <div class="my-4 text-justify indent-10 prose max-w-none dark:prose-invert">
                        {!! lc_content($page) !!}
                    </div>
                    @if (!empty($images) && is_array($images))
                        <div class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-1 lg:grid-cols-4">
                            @foreach ($images as $i => $url)
                                <a data-fancybox="gallery" href="{{ $url }}" aria-label="Photo gallery">
                                    <x-main.image src="{{ $url }}" :lazy="true"
                                        alt="Gallery image {{ $i + 1 }}"
                                        class="h-[250px] w-full overflow-hidden rounded-lg object-cover" />
                                </a>
                            @endforeach
                        </div>
                    @endif
                    @if ($page->files->isNotEmpty())
                        <div class="my-5 grid grid-cols-3 gap-5 md:grid-cols-4 lg:grid-cols-6">
                            @foreach ($page->files as $file)
                                <div
                                    class="rounded-lg border border-gray-400 bg-gray-300 px-2 py-5 text-center dark:bg-gray-800">
                                    <a href="{{ asset('storage/' . $file->file) }}" download
                                        class="flex flex-col items-center gap-2">
                                        <img src="/img/icons/file.webp" alt="Download icon" class="w-10 dark:invert" />
                                        {{ $file->name ?? basename($file->file) }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <x-main.sidebar />
        </div>
    </div>
</x-main-layout>
