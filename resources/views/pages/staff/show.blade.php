<x-main-layout :metaTitle="$metaTitle" :metaDescription="$metaDescription" :metaImage="$metaImage" :canonical="$canonical">
    <x-breadcrumb :menu="$menuModel" :submenu="$submenuModel" :multimenu="$multimenuModel" :page="$pageModel" :staff="$staff" />

    {{-- ══ HEADER ══ --}}
    {{-- Sarlavha: shu sahifa kontekstidagi lavozim (dekan, o'qituvchi va h.k.) --}}
    <x-page.show-header
        :title="$staff->name"
        :subtitle="lc_position($contextPosition ?? $staff)"
        :image="$staff->profile_photo_path ? asset('storage/' . $staff->profile_photo_path) : asset('img/default-avatar.webp')"
    />

    {{-- ══ CONTENT ══ --}}
    <div class="bg-gray-100 px-4 lg:px-0 py-10" x-data
        x-intersect.once.threshold.10="$el.classList.add('footer-in')">
        <div class="container mx-auto">
            <div class="grid grid-cols-4 gap-6">
                <div class="col-span-4 md:col-span-3 flex flex-col min-w-0 gap-6">

                    {{-- Barcha lavozimlari (bir nechta bo'limda ishlasa) --}}
                    @if($staff->pagePositions->count() > 0)
                        <div class="footer-anim rounded-2xl bg-white border border-gray-200 p-5"
                             style="transition-delay: 0.05s;">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                                {{ __('messages.positions') }}
                            </h3>
                            <div class="divide-y divide-gray-100">
                                @foreach($staff->pagePositions as $pos)
                                    <div class="py-2.5 flex items-start gap-3">
                                        <svg class="w-4 h-4 text-teal-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ lc_position($pos) }}</p>
                                            @if($pos->page)
                                                <p class="text-xs text-gray-500 mt-0.5">{{ lc_title($pos->page) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Bio --}}
                    <div class="footer-anim rounded-2xl bg-white border border-gray-200 p-6 md:p-8 flex-1"
                        style="transition-delay: 0.10s;">
                        <div class="prose max-w-none text-gray-700">
                            {!! lc_content($staff) !!}
                        </div>
                    </div>
                </div>
                <x-main.sidebar />
            </div>
        </div>
    </div>
</x-main-layout>
