<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo"></x-slot>

        {{-- Heading --}}
        <div class="mb-7">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-teal-50 mb-4">
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Parolni tiklash</h2>
            <p class="text-gray-400 text-sm mt-1">
                Email manzilingizni kiriting — parolni tiklash havolasini yuboramiz.
            </p>
        </div>

        {{-- Status message --}}
        @session('status')
            <div class="mb-5 p-3 bg-green-50 border border-green-100 rounded-xl text-sm text-green-700 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $value }}
            </div>
        @endsession

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="mb-5 p-4 bg-red-50 border border-red-100 rounded-xl">
                <p class="text-sm font-semibold text-red-600 mb-1">Xatolik yuz berdi!</p>
                <ul class="list-disc list-inside text-sm text-red-500 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email manzil</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           placeholder="example@sies.uz"
                           class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-white text-gray-800 text-sm
                                  placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent
                                  transition duration-200 @error('email') border-red-300 @enderror" />
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full py-3 px-4 rounded-xl font-semibold text-white text-sm tracking-wide shadow-md
                           bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800
                           focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2
                           transition-all duration-200 active:scale-[0.98]">
                Havola yuborish
            </button>
        </form>

        {{-- Back to login --}}
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-1.5 text-sm text-teal-600 hover:text-teal-800 font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kirishga qaytish
            </a>
        </div>

    </x-authentication-card>
</x-guest-layout>
