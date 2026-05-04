<x-main-layout :metaTitle="$metaTitle" :metaDescription="$metaDescription" :metaImage="$metaImage">
    <x-breadcrumb :menu="$menuModel" :submenu="$submenuModel ?? null" :multimenu="$multimenuModel ?? null" />

    {{-- ══ HEADER ══ --}}
    <x-page.show-header
        :title="lc_title($page)"
        :image="$page->imageUrl()"
        :date="$page->date?->format('Y-m-d')"
        :views="$page->views ?? 0"
    />

    {{-- ══ CONTENT ══ --}}
    <div class="bg-gray-100 px-4 lg:px-0 py-10" x-data
        x-intersect.once.threshold.10="$el.classList.add('footer-in')">
        <div class="container mx-auto">
            <div class="grid grid-cols-4 gap-6">

                <div class="footer-anim col-span-4 md:col-span-3 rounded-2xl overflow-hidden shadow-sm flex flex-col"
                    style="transition-delay: 0.10s;" x-data="{ tab: 'about' }">

                    {{-- Tab bar --}}
                    <div class="bg-gray-200 p-3 flex flex-wrap gap-2">
                        <button type="button"
                            :class="tab === 'about' ? 'bg-teal-800 border-teal-700 text-white' : 'bg-white border-gray-300 text-gray-600 hover:text-teal-800 hover:bg-gray-50'"
                            class="card-shine relative inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border overflow-hidden transition-all duration-200"
                            style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);"
                            @click="tab = 'about'">
                            {{ __('messages.about_section') }}
                        </button>
                        <button type="button"
                            :class="tab === 'employees' ? 'bg-teal-800 border-teal-700 text-white' : 'bg-white border-gray-300 text-gray-600 hover:text-teal-800 hover:bg-gray-50'"
                            class="card-shine relative inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border overflow-hidden transition-all duration-200"
                            style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);"
                            @click="tab = 'employees'">
                            {{ __('messages.section_staff') }}
                        </button>
                    </div>

                    {{-- About --}}
                    <section x-show="tab === 'about'" x-cloak class="bg-white p-6 md:p-8 flex-1">
                        <div class="prose max-w-none text-gray-700 text-justify indent-10">
                            {!! lc_content($page) !!}
                        </div>

                        @if ($page->files->isNotEmpty())
                            <div class="mt-6 pt-5 border-t border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                    {{ __('messages.files') ?? 'Fayllar' }}
                                </h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    @foreach ($page->files as $file)
                                        <a href="{{ asset('storage/' . $file->file) }}" download
                                            class="card-shine group flex flex-col items-center gap-2 rounded-xl p-4 overflow-hidden
                                                   bg-gray-100 border border-gray-200 hover:border-teal-800 text-center transition-colors">
                                            <img src="/img/icons/file.webp" alt=""
                                                class="w-8 opacity-60 group-hover:opacity-90 transition" />
                                            <span class="line-clamp-2 text-xs text-gray-600 group-hover:text-teal-800 transition-colors">
                                                {{ $file->name ?? basename($file->file) }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </section>

                    {{-- Staff --}}
                    <section x-show="tab === 'employees'" x-cloak class="bg-gray-50 p-6 flex-1" style="contain: layout style;">
                        @foreach ($page->staffCategories as $index => $category)
                            <div class="mb-10">
                                <div class="text-center mb-8">
                                    <h2 class="text-xl font-bold text-gray-800 mb-2">
                                        {{ $category->{'title_' . app()->getLocale()} }}
                                    </h2>
                                    <div class="w-32 h-0.5 mx-auto rounded"
                                        style="background: #0d9488;"></div>
                                </div>
                                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @forelse ($category->employees as $staffIndex => $staff)
                                        @if ($index === 0 && $staffIndex === 0)
                                            <div class="lg:col-span-3">
                                                <x-card.main-employee-card :employee="$staff" :locale="app()->getLocale()"
                                                    :menuModel="$menuModel" :submenuModel="$submenuModel ?? null"
                                                    :multimenuModel="$multimenuModel ?? null" :page="$page ?? null" :category="$category ?? null" />
                                            </div>
                                        @else
                                            <x-card.employee-card :employee="$staff" :locale="app()->getLocale()"
                                                :menuModel="$menuModel" :submenuModel="$submenuModel ?? null"
                                                :multimenuModel="$multimenuModel ?? null" :page="$page ?? null" :category="$category ?? null" />
                                        @endif
                                    @empty
                                        <div class="col-span-full text-center py-8">
                                            <p class="text-gray-400">{{ __('messages.staff_error') }}</p>
                                        </div>
                                    @endforelse
                                </div>
                                @if (!$loop->last)
                                    <div class="mt-10 border-t border-gray-200"></div>
                                @endif
                            </div>
                        @endforeach
                    </section>

                </div>

                <x-main.sidebar />
            </div>
        </div>
    </div>
</x-main-layout>
