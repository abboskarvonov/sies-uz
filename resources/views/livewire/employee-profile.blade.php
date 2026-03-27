<div class="space-y-6"
     x-data="{ saved: false, syncDone: false, syncError: '' }"
     x-on:profile-saved.window="saved = true; setTimeout(() => saved = false, 3500)"
     x-on:hemis-synced.window="syncDone = true; setTimeout(() => syncDone = false, 3500)"
     x-on:hemis-sync-error.window="syncError = $event.detail.message; setTimeout(() => syncError = '', 4000)">

    {{-- Flash messages --}}
    <div x-show="saved" x-transition x-cloak
         class="bg-teal-50 border border-teal-200 text-teal-800 text-sm px-4 py-3 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        Profil muvaffaqiyatli saqlandi.
    </div>
    <div x-show="syncDone" x-transition x-cloak
         class="bg-blue-50 border border-blue-200 text-blue-800 text-sm px-4 py-3 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        HEMIS ma'lumotlari yangilandi.
    </div>
    <div x-show="syncError" x-transition x-cloak
         class="bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-lg">
        <span x-text="syncError"></span>
    </div>

    {{-- HEMIS info card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">HEMIS ma'lumotlari</h2>
                <p class="text-xs text-gray-400 mt-0.5">HEMIS tizimidan olinadi — to'g'ridan-to'g'ri o'zgartirib bo'lmaydi.</p>
            </div>
            @if($user->hemis_employee_id)
                <button wire:click="syncFromHemis"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg
                               border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg wire:loading.remove wire:target="syncFromHemis" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <svg wire:loading wire:target="syncFromHemis" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    <span wire:loading.remove wire:target="syncFromHemis">HEMIS dan yangilash</span>
                    <span wire:loading wire:target="syncFromHemis">Yangilanmoqda...</span>
                </button>
            @endif
        </div>
        <div class="p-6 flex gap-5 items-start">
            <div class="flex-shrink-0">
                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                     class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
            </div>
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div>
                    <span class="text-gray-400 text-xs">Ism-familya</span>
                    <p class="font-medium text-gray-800 mt-0.5">{{ $user->name }}</p>
                </div>
                <div>
                    <span class="text-gray-400 text-xs">Ilmiy daraja</span>
                    <p class="font-medium text-gray-800 mt-0.5">{{ $user->academic_degree ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-gray-400 text-xs">Ilmiy unvon</span>
                    <p class="font-medium text-gray-800 mt-0.5">{{ $user->academic_rank ?? '—' }}</p>
                </div>
                @if($user->email)
                <div>
                    <span class="text-gray-400 text-xs">Email</span>
                    <p class="font-medium text-gray-800 mt-0.5">{{ $user->email }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Barcha lavozimlari ro'yxati --}}
        @if($user->pagePositions->isNotEmpty())
            <div class="border-t border-gray-100">
                <div class="px-6 py-3 bg-gray-50">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Lavozimlari</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($user->pagePositions as $pos)
                        <div class="px-6 py-3 flex items-start gap-3">
                            @if($pos->is_primary)
                                <span class="mt-0.5 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-teal-100 text-teal-700 shrink-0">
                                    Asosiy
                                </span>
                            @else
                                <span class="mt-0.5 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500 shrink-0">
                                    Qo'shimcha
                                </span>
                            @endif
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800">{{ lc_position($pos) }}</p>
                                @if($pos->page)
                                    <p class="text-xs text-gray-500 mt-0.5 truncate">{{ lc_title($pos->page) }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Editable form: photo + bio --}}
    <form wire:submit.prevent="save" class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Profil tahrirlash</h2>
            <p class="text-xs text-gray-400 mt-0.5">Rasm va bio ma'lumotlarini o'zgartirishingiz mumkin.</p>
        </div>
        <div class="p-6 space-y-6">

            {{-- Photo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Profil rasmi</label>
                <div class="flex items-center gap-4">
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="w-16 h-16 rounded-full object-cover border-2 border-teal-300">
                    @else
                        <img src="{{ $user->profile_photo_url }}" class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                    @endif
                    <label class="cursor-pointer inline-flex items-center gap-2 text-sm text-teal-700 border border-teal-300 bg-teal-50 hover:bg-teal-100 px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Rasm yuklash
                        <input type="file" wire:model="photo" accept="image/*" class="hidden">
                    </label>
                    <span class="text-xs text-gray-400">JPG, PNG, WEBP — maks 2MB</span>
                </div>
                @error('photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div wire:loading wire:target="photo" class="text-xs text-teal-600 mt-1">Yuklanmoqda...</div>
            </div>

            {{-- Bio --}}
            <div x-data="{ tab: 'uz' }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                <div class="flex border-b border-gray-200 mb-4">
                    <button type="button" @click="tab='uz'"
                        :class="tab==='uz' ? 'border-b-2 border-teal-600 text-teal-700 font-medium' : 'text-gray-500 hover:text-gray-700'"
                        class="px-4 py-2 text-sm">O'zbek</button>
                    <button type="button" @click="tab='ru'"
                        :class="tab==='ru' ? 'border-b-2 border-teal-600 text-teal-700 font-medium' : 'text-gray-500 hover:text-gray-700'"
                        class="px-4 py-2 text-sm">Русский</button>
                    <button type="button" @click="tab='en'"
                        :class="tab==='en' ? 'border-b-2 border-teal-600 text-teal-700 font-medium' : 'text-gray-500 hover:text-gray-700'"
                        class="px-4 py-2 text-sm">English</button>
                </div>
                <div x-show="tab==='uz'">
                    <textarea wire:model="content_uz" rows="5"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-colors resize-none"
                        placeholder="O'zingiz haqingizda qisqacha ma'lumot..."></textarea>
                    @error('content_uz') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div x-show="tab==='ru'">
                    <textarea wire:model="content_ru" rows="5"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-colors resize-none"
                        placeholder="Краткая информация о себе..."></textarea>
                    @error('content_ru') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div x-show="tab==='en'">
                    <textarea wire:model="content_en" rows="5"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-colors resize-none"
                        placeholder="Brief information about yourself..."></textarea>
                    @error('content_en') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
            <button type="submit"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-not-allowed"
                class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors">
                <span wire:loading.remove wire:target="save">Saqlash</span>
                <span wire:loading wire:target="save">Saqlanmoqda...</span>
            </button>
        </div>
    </form>

</div>
