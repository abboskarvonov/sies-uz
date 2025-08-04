<div class="container mx-auto py-10 lg:py-20 px-4 lg:px-0">
    <h1 class="flex items-center gap-2 text-xl font-medium uppercase md:text-2xl">
        <img src="/img/icons/013-book-1.webp" alt="Book icon" class="w-6 dark:invert" />
        Ilmiy faoliyat
    </h1>

    <div class="mt-5 grid grid-cols-1 justify-between gap-3 md:mt-10 md:grid-cols-2 md:gap-8">
        @if ($announcementsWithActivity->isNotEmpty())
            @foreach ($announcementsWithActivity as $item)
                <div
                    class="flex items-center justify-between overflow-hidden rounded-xl bg-gray-100 p-1 dark:bg-gray-700 md:p-4">
                    <!-- Rasmlar -->
                    <div class="relative h-52 w-2/5 overflow-hidden rounded-lg shadow-md">
                        <x-main.image src="{{ asset('storage/' . $item->image) }}"
                            alt="{{ $item->{'title_' . app()->getLocale()} }}" class="h-full w-full object-cover" />
                    </div>

                    <!-- Matn qismi -->
                    <div class="w-3/5 px-4">
                        <a href="/pages-view/example-slug" class="text-md">
                            {{ $item->{'title_' . app()->getLocale()} }}
                        </a>
                        <div class="lg:text-md mt-4 flex items-center gap-5 text-sm lg:gap-10">
                            <div class="flex items-center gap-2">
                                <img src="/img/icons/011-clock.webp" alt="Book icon" class="w-3 dark:invert" />
                                {{ $item->date?->format('Y-m-d') ?? 'No date' }}
                            </div>
                            <div class="flex items-center gap-2">
                                <img src="/img/icons/012-user.webp" alt="Book icon" class="w-3 dark:invert" />
                                {{ $item->views ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
