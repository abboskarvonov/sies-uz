<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo"></x-slot>

        {{-- Heading --}}
        <div class="mb-7">
            <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Ro'yxatdan o'tish</h2>
            <p class="text-gray-400 text-sm mt-1">Yangi hisob yaratish uchun ma'lumotlarni kiriting</p>
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

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            {{-- Full name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">F.I.SH.</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                           required autofocus autocomplete="name"
                           placeholder="Ism Familiya Sharif"
                           class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-white text-gray-800 text-sm
                                  placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent
                                  transition duration-200 @error('name') border-red-300 @enderror" />
                </div>
            </div>

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
                           required autocomplete="username"
                           placeholder="example@sies.uz"
                           class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-white text-gray-800 text-sm
                                  placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent
                                  transition duration-200 @error('email') border-red-300 @enderror" />
                </div>
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Parol</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>
                    <input id="password" type="password" name="password"
                           required autocomplete="new-password"
                           placeholder="Kamida 8 ta belgi"
                           class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-white text-gray-800 text-sm
                                  placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent
                                  transition duration-200 @error('password') border-red-300 @enderror" />
                </div>
            </div>

            {{-- Confirm password --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Parolni tasdiqlash
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </span>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           required autocomplete="new-password"
                           placeholder="Parolni qaytadan kiriting"
                           class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-white text-gray-800 text-sm
                                  placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent
                                  transition duration-200" />
                </div>
            </div>

            {{-- Terms --}}
            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="flex items-start gap-3 pt-1">
                    <input type="checkbox" name="terms" id="terms" required
                           class="mt-0.5 w-4 h-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500 cursor-pointer shrink-0" />
                    <label for="terms" class="text-sm text-gray-600 leading-relaxed cursor-pointer">
                        {!! __('I agree to the :terms_of_service and :privacy_policy', [
                            'terms_of_service' =>
                                '<a target="_blank" href="' . route('terms.show') . '"
                                    class="text-teal-600 hover:text-teal-700 font-medium underline">'
                                . __('Terms of Service') . '</a>',
                            'privacy_policy' =>
                                '<a target="_blank" href="' . route('policy.show') . '"
                                    class="text-teal-600 hover:text-teal-700 font-medium underline">'
                                . __('Privacy Policy') . '</a>',
                        ]) !!}
                    </label>
                </div>
            @endif

            {{-- Submit --}}
            <div class="pt-1">
                <button type="submit"
                        class="w-full py-3 px-4 rounded-xl font-semibold text-white text-sm tracking-wide shadow-md
                               bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800
                               focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2
                               transition-all duration-200 active:scale-[0.98]">
                    Ro'yxatdan o'tish
                </button>
            </div>

            {{-- Divider --}}
            <div class="flex items-center gap-3">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-xs text-gray-400">yoki</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            {{-- Login link --}}
            <a href="{{ route('login') }}"
               class="flex items-center justify-center w-full py-3 px-4 rounded-xl font-semibold text-sm
                      border-2 border-teal-600 text-teal-700 hover:bg-teal-50
                      focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2
                      transition-all duration-200 active:scale-[0.98]">
                Hisobingiz bormi? Kirish
            </a>
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
