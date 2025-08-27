<div class="w-full lg:py-20 px-4 lg:px-0">
    <div class="container mx-auto">
        <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl">
            <img src="/img/icons/hastag.webp" alt="Hash icon" class="w-6 dark:invert" />
            {{ __('messages.tags') }}
        </h1>
        <div class="mt-6 flex flex-wrap gap-3">
            @if ($tags)
                @foreach ($tags as $tag)
                    <a href="{{ route('tags.show', ['slug' => $tag->slug]) }}"
                        class="rounded-lg flex items-center gap-2 bg-gray-50 px-6 py-4 text-sm font-medium shadow hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-600">
                        <img src="/img/icons/speech-bubble.webp" alt="Tags" class="w-5 dark:invert" />
                        {{ $tag->name }}
                    </a>
                @endforeach
            @endif
        </div>
    </div>
</div>
