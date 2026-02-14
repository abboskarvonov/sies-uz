<x-main-layout :metaTitle="$metaTitle" :metaDescription="$metaDescription" :metaImage="$metaImage">
    <x-breadcrumb :menu="$menuModel" :submenu="$submenuModel ?? null" :multimenu="$multimenuModel ?? null" />
    <div class="dark:bg-gray-6 page-header relative mb-4 w-full bg-gray-200">
        <div class="absolute z-10 h-full w-full bg-white/75 dark:bg-gray-950/80"></div>
        <div class="container mx-auto relative z-20 grid grid-cols-5 gap-5 py-6">
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
    <div class="container mx-auto my-10 rounded-lg bg-gray-100 py-6 shadow dark:bg-gray-700 px-4">
        <div class="grid grid-cols-4 gap-4">
            <div class="col-span-4 rounded-xl bg-background md:col-span-3 bg-white dark:bg-gray-800 shadow p-4"
                x-data="{ tab: 'about' }">
                {{-- Tab boshligi --}}
                <div class="w-full border-b border-gray-200 dark:border-gray-600">
                    <nav class="flex gap-2" role="tablist" aria-label="Center tabs">
                        <button type="button" class="px-6 py-2 rounded-md"
                            :class="tab === 'about'
                                ?
                                'bg-gray-800 text-white' :
                                'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-white'"
                            @click="tab = 'about'" role="tab" :aria-selected="(tab === 'about').toString()"
                            aria-controls="tab-about" id="tab-about-trigger">
                            {{ __('messages.about_center') }}
                        </button>

                        <button type="button" class="px-6 py-2 rounded-md"
                            :class="tab === 'employees'
                                ?
                                'bg-gray-800 text-white' :
                                'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-white'"
                            @click="tab = 'employees'" role="tab" :aria-selected="(tab === 'employees').toString()"
                            aria-controls="tab-employees" id="tab-employees-trigger">
                            {{ __('messages.center_staff') }}
                        </button>
                    </nav>
                </div>

                {{-- About (content) --}}
                <section x-show="tab === 'about'" x-cloak id="tab-about" role="tabpanel"
                    aria-labelledby="tab-about-trigger" class="mt-4">
                    <div class="my-4 text-justify indent-10 prose max-w-none dark:prose-invert">
                        {!! lc_content($page) !!}
                    </div>
                </section>

                {{-- Employees --}}
                <section x-show="tab === 'employees'" x-cloak id="tab-employees" role="tabpanel"
                    aria-labelledby="tab-employees-trigger" class="mt-4">

                    <div class="min-h-screen">
                        {{-- Main Content --}}
                        <div class="container mx-auto px-4">
                            @foreach ($page->staffCategories as $index => $category)
                                <div class="mb-10">
                                    {{-- Category Title --}}
                                    <div class="text-center mb-8">
                                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">
                                            {{ $category->{'title_' . app()->getLocale()} }}
                                        </h2>
                                        <div
                                            class="w-44 h-1 bg-gradient-to-r from-gray-300 to-gray-700 mx-auto rounded">
                                        </div>
                                    </div>

                                    {{-- Employees Grid --}}
                                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
                                        @forelse ($category->staffMembers as $staffIndex => $staff)
                                            @if ($index === 0 && $staffIndex === 0)
                                                {{-- First employee in first category - Featured Card --}}
                                                <div class="lg:col-span-3">
                                                    <x-card.main-employee-card :employee="$staff" :locale="app()->getLocale()"
                                                        :menuModel="$menuModel" :submenuModel="$submenuModel ?? null" :multimenuModel="$multimenuModel ?? null"
                                                        :page="$page ?? null" :category="$category ?? null" />
                                                </div>
                                            @else
                                                {{-- Regular Employee Cards --}}
                                                <x-card.employee-card :employee="$staff" :locale="app()->getLocale()"
                                                    :menuModel="$menuModel" :submenuModel="$submenuModel ?? null" :multimenuModel="$multimenuModel ?? null"
                                                    :page="$page ?? null" :category="$category ?? null" />
                                            @endif
                                        @empty
                                            <div class="col-span-full text-center py-12">
                                                <p class="text-lg text-gray-600 dark:text-gray-300">
                                                    {{ __('messages.staff_error') }}
                                                </p>
                                            </div>
                                        @endforelse
                                    </div>

                                    @if (!$loop->last)
                                        <div class="mt-12 border-b-2 border-gray-200 dark:border-gray-700"></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- @foreach ($page->staffCategories as $category)
                        <div class="border-b pb-4">
                            <h2 class="text-xl font-bold my-4 text-center text-gray-800 dark:text-white">
                                {{ $category->{'title_' . app()->getLocale()} }}
                            </h2>

                            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                                @forelse ($category->staffMembers as $staff)
                                    <x-card.employee-card :employee="$staff" :locale="$locale" :menuModel="$menuModel"
                                        :submenuModel="$submenuModel" :multimenuModel="$multimenuModel" :page="null" />
                                @empty
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        {{ __('messages.staff_error') }}
                                    </p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach --}}
                </section>
            </div>

            {{-- O‘ng panel (sidebar) --}}
            <x-main.sidebar />
        </div>
    </div>
</x-main-layout>
