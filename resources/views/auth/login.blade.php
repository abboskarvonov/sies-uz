<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo"></x-slot>

        {{-- Heading --}}
        <div class="mb-8">
            <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Xush kelibsiz!</h2>
            <p class="text-gray-400 text-sm mt-1">Hisobingizga kirish uchun ma'lumotlarni kiriting</p>
        </div>

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

        @session('status')
            <div class="mb-5 p-3 bg-green-50 border border-green-100 rounded-xl text-sm text-green-700">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Email manzil
                </label>
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
                                  transition duration-200 @error('email') border-red-300 focus:ring-red-400 @enderror" />
                </div>
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Parol
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>
                    <input id="password" type="password" name="password"
                           required autocomplete="current-password"
                           placeholder="••••••••"
                           class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-white text-gray-800 text-sm
                                  placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent
                                  transition duration-200" />
                </div>
            </div>

            {{-- Remember + Forgot --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="remember_me" name="remember"
                           class="w-4 h-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500 cursor-pointer" />
                    <span class="text-sm text-gray-600">Meni eslab qol</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-teal-600 hover:text-teal-800 font-medium transition-colors">
                        Parolni unutdingizmi?
                    </a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full py-3 px-4 rounded-xl font-semibold text-white text-sm tracking-wide shadow-md
                           bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800
                           focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2
                           transition-all duration-200 active:scale-[0.98]">
                Kirish
            </button>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-1">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-xs text-gray-400">yoki</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            {{-- Register link --}}
            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="flex items-center justify-center w-full py-3 px-4 rounded-xl font-semibold text-sm
                          border-2 border-teal-600 text-teal-700 hover:bg-teal-50
                          focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2
                          transition-all duration-200 active:scale-[0.98]">
                    Ro'yxatdan o'tish
                </a>
            @endif
        </form>

        {{-- Back to site --}}
        <div class="mt-6 text-center">
            <a href="/" class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-teal-600 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Asosiy sahifaga qaytish
            </a>
        </div>

    </x-authentication-card>
</x-guest-layout>
