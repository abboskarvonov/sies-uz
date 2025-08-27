<x-main-layout :metaTitle="$metaTitle" :metaDescription="$metaDescription" :metaImage="$metaImage" :canonical="$canonical">
    {{-- Breadcrumb --}}
    <x-breadcrumb :menu="$menuModel" />

    <div class="container mx-auto my-10 rounded-lg bg-gray-50 py-6 shadow dark:bg-gray-700 px-4">
        <h1 class="text-xl font-medium uppercase tracking-tight">
            {{ lc_title($menuModel) }}
        </h1>

        @if ($submenus->isEmpty())
            <div class="mt-6 rounded-lg bg-white p-6 text-center dark:bg-gray-800">
                <p class="text-gray-600 dark:text-gray-300">Hozircha ma’lumotlar tayyorlanmoqda.</p>
            </div>
        @else
            <div class="grid grid-cols-4 gap-4 py-5">
                <div class="col-span-4 md:col-span-3">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($submenus as $sm)
                            @php
                                $title = lc_title($sm);
                                $img = $sm->image ? asset('storage/' . $sm->image) : asset('img/folder.webp');
                                // 3-segmentli sahifa: /menu/submenu/multimenu
                                $url = localized_page_route($menuModel, $sm);
                            @endphp

                            <div class="rounded-xl overflow-hidden bg-white dark:bg-gray-800 shadow">
                                <a href="{{ $url }}">
                                    <x-main.image :src="$img" :alt="$title" class="h-56 w-full object-cover" />
                                </a>

                                <div class="p-4 space-y-2">
                                    <a href="{{ $url }}" class="block">
                                        <h3 class="text-lg text-center font-semibold text-gray-900 dark:text-white">
                                            {{ $title }}
                                        </h3>
                                    </a>

                                    <div class="grid">
                                        <x-button class="mt-3">
                                            <a href="{{ $url }}" class="flex items-center gap-2">
                                                {{ __('messages.read_more') }}
                                                <svg class="h-3.5 w-3.5 rtl:rotate-180" aria-hidden="true"
                                                    viewBox="0 0 14 10" fill="none">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M1 5h12m0 0L9 1m4 4L9 9" />
                                                </svg>
                                            </a>
                                        </x-button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $submenus->links() }}
                    </div>
                </div>
                <x-main.sidebar />
            </div>
        @endif
    </div>
</x-main-layout>
