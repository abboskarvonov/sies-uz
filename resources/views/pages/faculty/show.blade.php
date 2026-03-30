<x-main-layout :metaTitle="$metaTitle" :metaDescription="$metaDescription" :metaImage="$metaImage">
    <x-breadcrumb :menu="$menuModel" :submenu="$submenuModel ?? null" :multimenu="$multimenuModel ?? null" :page="$page ?? null" />

    {{-- ══ HEADER ══ --}}
    <x-page.show-header :title="lc_title($page)" :image="asset('storage/' . $page->image)" :date="$page->date?->format('Y-m-d')" :views="$page->views ?? 0" />

    {{-- ══ CONTENT ══ --}}
    <div class="bg-gray-100 px-4 lg:px-0 py-10" x-data x-intersect.once.threshold.10="$el.classList.add('footer-in')">
        <div class="container mx-auto">
            <div class="grid grid-cols-4 gap-6">

                <div class="footer-anim col-span-4 md:col-span-3 rounded-2xl overflow-hidden shadow-sm flex flex-col"
                    style="transition-delay: 0.10s;" x-data="{ tab: 'about' }">

                    {{-- Tab bar --}}
                    <div class="bg-gray-200 p-3 flex flex-wrap gap-2">
                        <button type="button"
                            :class="tab === 'about' ? 'bg-teal-800 border-teal-700 text-white' :
                                'bg-white border-gray-300 text-gray-600 hover:text-teal-800 hover:bg-gray-50'"
                            class="card-shine relative inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border overflow-hidden transition-all duration-200"
                            style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);" @click="tab = 'about'">
                            {{ __('messages.about_faculty') }}
                        </button>
                        <button type="button"
                            :class="tab === 'departments' ? 'bg-teal-800 border-teal-700 text-white' :
                                'bg-white border-gray-300 text-gray-600 hover:text-teal-800 hover:bg-gray-50'"
                            class="card-shine relative inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border overflow-hidden transition-all duration-200"
                            style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);" @click="tab = 'departments'">
                            {{ __('messages.faculty_departments') }}
                        </button>
                        <button type="button"
                            :class="tab === 'employees' ? 'bg-teal-800 border-teal-700 text-white' :
                                'bg-white border-gray-300 text-gray-600 hover:text-teal-800 hover:bg-gray-50'"
                            class="card-shine relative inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border overflow-hidden transition-all duration-200"
                            style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);" @click="tab = 'employees'">
                            {{ __('messages.faculty_staff') }}
                        </button>
                    </div>

                    {{-- Fakultet haqida --}}
                    <section x-show="tab === 'about'" x-cloak class="bg-white p-6 md:p-8 flex-1">
                        <div class="prose max-w-none text-gray-700 text-justify indent-10">
                            {!! lc_content($page) !!}
                        </div>

                    </section>

                    {{-- Fakultet kafedralari --}}
                    <section x-show="tab === 'departments'" x-cloak class="bg-gray-50 p-6 flex-1">
                        @if ($page->childPages->isEmpty())
                            <div class="flex flex-col items-center justify-center py-16 text-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <p class="text-gray-400 text-sm">{{ __('messages.no_departments') }}</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($page->childPages as $dept)
                                    @php
                                        $deptUrl = $dept->menu_id
                                            ? localized_page_route($dept->menu, $dept->submenu, $dept->multimenu, $dept)
                                            : null;
                                    @endphp
                                    <div
                                        class="group bg-white rounded-xl border border-gray-200 hover:border-teal-500/50 hover:shadow-md transition-all duration-300 overflow-hidden">
                                        <div class="flex items-stretch">
                                            {{-- Sol rang chizig'i --}}
                                            <div class="w-1 bg-teal-600 shrink-0 rounded-l-xl"></div>

                                            <div class="flex-1 p-5">
                                                {{-- Nom --}}
                                                <h3
                                                    class="font-semibold text-gray-800 text-sm leading-snug group-hover:text-teal-700 transition-colors">
                                                    {{ lc_title($dept) }}
                                                </h3>

                                                {{-- Xodimlar soni --}}
                                                @if ($dept->employees_count > 0)
                                                    <p class="mt-2 text-xs text-gray-400 flex items-center gap-1.5">
                                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        {{ $dept->employees_count }} {{ __('messages.employees') }}
                                                    </p>
                                                @endif

                                                {{-- Link --}}
                                                @if ($deptUrl)
                                                    <a href="{{ $deptUrl }}"
                                                        class="mt-3 inline-flex items-center gap-1 text-xs font-medium text-teal-700 hover:text-teal-800 transition-colors">
                                                        {{ __('messages.read_more') }}
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </section>

                    {{-- Xodimlar --}}
                    <section x-show="tab === 'employees'" x-cloak class="bg-gray-50 p-6 flex-1" style="contain: layout style;">
                        @forelse ($page->staffCategories as $index => $category)
                            <div class="mb-10">
                                <div class="text-center mb-8">
                                    <h2 class="text-xl font-bold text-gray-800 mb-2">
                                        {{ $category->{'title_' . app()->getLocale()} }}
                                    </h2>
                                    <div class="w-32 h-0.5 mx-auto rounded" style="background: #0d9488;"></div>
                                </div>
                                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @forelse ($category->employees as $staffIndex => $staff)
                                        @if ($index === 0 && $staffIndex === 0)
                                            <div class="lg:col-span-3">
                                                <x-card.main-employee-card :employee="$staff" :locale="app()->getLocale()"
                                                    :menuModel="$menuModel" :submenuModel="$submenuModel ?? null" :multimenuModel="$multimenuModel ?? null"
                                                    :page="$page ?? null" :category="$category ?? null" />
                                            </div>
                                        @else
                                            <x-card.employee-card :employee="$staff" :locale="app()->getLocale()"
                                                :menuModel="$menuModel" :submenuModel="$submenuModel ?? null" :multimenuModel="$multimenuModel ?? null"
                                                :page="$page ?? null" :category="$category ?? null" />
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
                        @empty
                            <div class="flex flex-col items-center justify-center py-16 text-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="text-gray-400 text-sm">{{ __('messages.staff_error') }}</p>
                            </div>
                        @endforelse
                    </section>

                </div>

                <x-main.sidebar />
            </div>
        </div>
    </div>
</x-main-layout>
