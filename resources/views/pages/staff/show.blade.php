<x-main-layout :metaTitle="$metaTitle" :metaDescription="$metaDescription" :metaImage="$metaImage" :canonical="$canonical">
    <x-breadcrumb :menu="$menuModel" :submenu="$submenuModel" :multimenu="$multimenuModel" :page="$pageModel" :staff="$staff" />

    {{-- ══ HEADER ══ --}}
    <x-page.show-header
        :title="$staff->name"
        :subtitle="lc_position($staff)"
        :image="$staff->profile_photo_path ? asset('storage/' . $staff->profile_photo_path) : asset('img/default-avatar.webp')"
    />

    {{-- ══ CONTENT ══ --}}
    <div class="bg-gray-100 px-4 lg:px-0 py-10" x-data
        x-intersect.once.threshold.10="$el.classList.add('footer-in')">
        <div class="container mx-auto">
            <div class="grid grid-cols-4 gap-6">
                <div class="col-span-4 md:col-span-3 flex flex-col">
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
