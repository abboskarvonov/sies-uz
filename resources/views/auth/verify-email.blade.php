<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo"></x-slot>

        {{-- Icon + Heading --}}
        <div class="mb-7">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-teal-50 mb-4">
                <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Email tasdiqlash</h2>
            <p class="text-gray-400 text-sm mt-1 leading-relaxed">
                Hisobingizni faollashtirish uchun emailingizga yuborilgan havolaga bosing.
                Xat kelmagan bo'lsa, qayta yuborishingiz mumkin.
            </p>
        </div>

        {{-- Success status --}}
        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 p-4 bg-green-50 border border-green-100 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-sm text-green-700">
                    Yangi tasdiqlash havolasi email manzilingizga muvaffaqiyatli yuborildi.
                </p>
            </div>
        @endif

        {{-- Resend button --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                    class="w-full py-3 px-4 rounded-xl font-semibold text-white text-sm tracking-wide shadow-md
                           bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800
                           focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2
                           transition-all duration-200 active:scale-[0.98]">
                Tasdiqlash xatini qayta yuborish
            </button>
        </form>

        {{-- Secondary actions --}}
        <div class="mt-5 flex items-center justify-between">
            <a href="{{ route('profile.show') }}"
               class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-teal-600 font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profilni tahrirlash
            </a>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-red-500 font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Chiqish
                </button>
            </form>
        </div>

    </x-authentication-card>
</x-guest-layout>
