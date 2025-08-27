<x-main-layout :metaTitle="$metaTitle" :metaDescription="$metaDescription" :metaImage="$metaImage" :canonical="$canonical">
    <x-breadcrumb :menu="$menuModel" :submenu="$submenuModel" :multimenu="$multimenuModel" :page="$pageModel" :staff="$staff" />
    <div class="dark:bg-gray-6 page-header relative mb-4 w-full bg-gray-200">
        <div class="absolute z-10 h-full w-full bg-white/75 dark:bg-gray-950/80"></div>
        <div class="container mx-auto relative z-20 grid grid-cols-5 gap-5 py-6">
            <div class="col-span-5 grid place-content-center justify-start space-y-5 md:col-span-3">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('img/icons/user-tie.webp') }}" class="w-6 h-6" alt="">
                    <h1 class="text-2xl font-medium uppercase tracking-tight">{{ lc_name($staff) }}</h1>
                </div>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('img/icons/checklist.webp') }}" class="w-5 h-5" alt="">
                        <p class="text-gray-600 dark:text-gray-300">{{ lc_position($staff) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex space-x-2">
                        <x-icon-button
                            onclick="window.open('https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($staff->{'name_' . $locale}) }}','_blank')">
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
                <x-main.image src="{{ asset('storage/' . $staff->image) }}" alt="{{ lc_name($staff) }}"
                    class="max-h-[480px] w-full rounded-lg object-cover shadow" />
            </div>
        </div>
    </div>
    <div class="container mx-auto my-10 rounded-lg bg-gray-100 py-6 shadow dark:bg-gray-700 px-4">
        <div class="grid grid-cols-4 gap-4">
            <div class="col-span-4 rounded-xl bg-background md:col-span-3" x-data="{ tab: 'about' }">
                <div class="rounded-xl bg-white dark:bg-gray-800 shadow p-4">
                    <div class="prose max-w-none dark:prose-invert">
                        {!! lc_content($staff) !!}
                    </div>
                </div>
            </div>
            {{-- O‘ng panel (sidebar) --}}
            <x-main.sidebar />
        </div>
    </div>
</x-main-layout>
