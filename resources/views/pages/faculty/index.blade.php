<x-main-layout :metaTitle="$metaTitle" :metaImage="$metaImage">
    <x-breadcrumb :menu="$menuModel" :submenu="$submenuModel ?? null" :multimenu="$multimenuModel ?? null" />
    <div class="container mx-auto my-10 rounded-lg bg-gray-50 py-6 shadow dark:bg-gray-700 px-4">
        <h1 class="text-xl font-medium uppercase tracking-tight">
            {{ lc_title($multimenuModel) }}
        </h1>
        <div class="grid grid-cols-4 gap-4 py-5">
            <div class="col-span-4 md:col-span-3">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @if ($pages->count() > 0)
                        @foreach ($pages as $page)
                            <x-card.card :page="$page" :menuModel="$menuModel" :submenuModel="$submenuModel" :multimenuModel="$multimenuModel" />
                        @endforeach
                    @endif
                </div>
                <div class="mt-6">
                    {{ $pages->links() }}
                </div>
            </div>
            <x-main.sidebar />
        </div>
    </div>
</x-main-layout>
