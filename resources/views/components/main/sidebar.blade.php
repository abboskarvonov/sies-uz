<div class="col-span-4 mt-10 space-y-5 md:col-span-1 md:mt-0">
    {{-- Yil uchun banner --}}
    <div class="border rounded-lg shadow bg-white dark:bg-gray-800 dark:border-gray-500">
        <div class="p-2">
            <img src="{{ asset('img/year.webp') }}" alt="SamISI" class="rounded" />
        </div>
        <h1 class="px-2 py-3 text-center text-xl tracking-tight md:px-4">
            {{ __('messages.year_name') }}
        </h1>
    </div>

    {{-- Menyular --}}
    <div class="py-3 border rounded-lg shadow bg-white dark:bg-gray-800 dark:border-gray-500">
        <h1 class="border-b border-b-gray-400 pb-2 text-center text-xl tracking-tight dark:border-b-gray-600">
            {{ __('messages.menu') }}
        </h1>
        <div class="mt-3 grid gap-2 px-3">
            @foreach ($menus as $menu)
                <a href="{{ localized_page_route($menu) }}"
                    class="block w-full text-center rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                    {{ $menu->{'title_' . app()->getLocale()} }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Teglar --}}
    <div class="py-3 border rounded-lg shadow bg-white dark:bg-gray-800 dark:border-gray-500">
        <h1 class="border-b border-b-gray-400 pb-2 text-center text-xl tracking-tight dark:border-b-gray-600">
            {{ __('messages.tags') }}
        </h1>
        <div class="mt-3 flex flex-wrap justify-center gap-2 px-4">
            @foreach ($tags as $tag)
                <a href="{{ route('tags.show', ['slug' => $tag->slug]) }}"
                    class="flex items-center gap-1 rounded-lg border px-3 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700">
                    <img src="{{ asset('img/icons/hastag.webp') }}" alt="SamISI" class="w-4 dark:invert" />
                    {{ $tag->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>
