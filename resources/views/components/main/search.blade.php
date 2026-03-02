<div x-data="{ customModalOpen: false }">
    <!-- Trigger button -->
    <x-icon-button @click="customModalOpen = true">
        <img src="{{ asset('img/icons/007-search.webp') }}" class="w-4 invert" alt="Search icon" />
        <span class="sr-only">Open Search Modal</span>
    </x-icon-button>

    {{-- x-teleport: modal body ga ko'chiriladi — backdrop-filter stacking context muammosini hal qiladi --}}
    <template x-teleport="body">
        <div x-show="customModalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" x-cloak
            class="fixed inset-0 z-9999 flex items-center justify-center backdrop-blur-sm bg-black/50" aria-modal="true"
            role="dialog" @click.self="customModalOpen = false" @keydown.escape.window="customModalOpen = false">

            <div x-show="customModalOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-lg mx-4 rounded-2xl overflow-hidden
                       bg-linear-to-br from-teal-900/85 to-teal-950/90
                       backdrop-blur-xl
                       border border-teal-700/40"
                style="box-shadow: 0 25px 50px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.06);">

                <button @click="customModalOpen = false"
                    class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full
                           text-teal-300 hover:text-white hover:bg-teal-800/60 transition-colors"
                    aria-label="Close">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <form action="{{ route('search') }}" method="GET" class="flex items-center gap-3 p-5">
                    <input type="text" name="q" placeholder="{{ __('messages.search_text') ?? 'Izlash...' }}"
                        autofocus
                        class="w-full px-4 py-2.5 rounded-xl text-sm
                               bg-teal-800/50
                               border border-teal-700/40 focus:border-teal-400
                               text-white placeholder-teal-400/70
                               outline-none transition-colors" />
                    <button type="submit"
                        class="shrink-0 px-5 py-2.5 rounded-xl
                               bg-teal-500 hover:bg-teal-400
                               text-white text-sm font-semibold transition-colors">
                        {{ __('messages.search_text') }}
                    </button>
                </form>
            </div>
        </div>
    </template>
</div>
