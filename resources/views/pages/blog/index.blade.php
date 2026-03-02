<x-main-layout :metaTitle="$metaTitle" :metaImage="$metaImage">
    <x-breadcrumb :menu="$menuModel" :submenu="$submenuModel ?? null" :multimenu="$multimenuModel ?? null" />

    <div class="bg-gray-100 px-4 lg:px-0 py-10" x-data x-intersect.once.threshold.10="$el.classList.add('footer-in')">
        <div class="container mx-auto">

            {{-- Title --}}
            <h1
                class="flex items-center gap-3 text-xl font-medium uppercase md:text-2xl text-teal-800 mb-8 footer-anim footer-anim-d1">
                <span
                    class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-700/10 border border-teal-700/20 shrink-0">
                    <img src="{{ asset('img/icons/010-ssd.webp') }}" alt="Icon" class="w-5 h-5" />
                </span>
                {{ lc_title($multimenuModel ?? ($submenuModel ?? $menuModel)) }}
            </h1>

            <div class="grid grid-cols-4 gap-6">

                {{-- Cards grid --}}
                <div class="col-span-4 md:col-span-3">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($pages as $page)
                            <div class="footer-anim h-full"
                                style="transition-delay: {{ number_format(0.1 + min($loop->index, 8) * 0.1, 2) }}s;">
                                <x-card.card :page="$page" :menuModel="$menuModel" :submenuModel="$submenuModel" :multimenuModel="$multimenuModel" />
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $pages->links() }}
                    </div>
                </div>

                {{-- Sidebar --}}
                <x-main.sidebar />

            </div>
        </div>
    </div>
</x-main-layout>
