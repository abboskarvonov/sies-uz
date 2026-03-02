<footer class="w-full bg-teal-950 border-t-4 border-teal-500/30">

    {{-- Katta Xarita Qismi --}}
    <div class="w-full h-112.5 overflow-hidden bg-teal-900/50" id="map-container"
        data-map-src="{{ $siteSettings?->map_embed_url ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3602.795078633065!2d66.95965593203682!3d39.652679673533754!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3f4d18d09d2a9295%3A0x5642746f9fcbed8!2sSamarkand%20State%20Institute%20of%20Economics%20and%20Service!5e0!3m2!1sen!2s!4v1710216853970!5m2!1sen!2s' }}">
        <div class="flex items-center justify-center h-full text-teal-600">
            <svg class="w-12 h-12 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
    </div>

    <div class="w-full bg-teal-950">
        <div class="container mx-auto px-4 lg:px-0 py-12 lg:py-16" x-data
            x-intersect.once.threshold.20="$el.classList.add('footer-in')">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">

                {{-- 1-Ustun: Logo va Ijtimoiy Tarmoqlar --}}
                <div class="space-y-4 footer-anim footer-anim-d1">
                    <div class="flex items-center gap-2">
                        <img src="{{ $siteSettings?->logoUrl() ?? asset('img/logo.webp') }}"
                            alt="{{ $siteSettings?->siteName() ?? __('messages.app_name') }}" class="w-auto max-w-40" />
                        <h1 class="text-center text-lg font-bold uppercase lg:text-xl text-white">
                            {{ $siteSettings?->siteName() ?? __('messages.app_name') }}
                        </h1>
                    </div>

                    {{-- Ijtimoiy tarmoqlar --}}
                    <div class="flex space-x-3 pt-4">
                        @if ($siteSettings?->telegram_url)
                            <a href="{{ $siteSettings->telegram_url }}" target="_blank" aria-label="Telegram"
                                class="opacity-80 hover:opacity-100 transition-opacity">
                                <img src="{{ asset('img/icons/telegram.webp') }}" alt="Telegram icon" class="w-7 h-7"
                                    width="28px" height="28px" />
                            </a>
                        @endif
                        @if ($siteSettings?->facebook_url)
                            <a href="{{ $siteSettings->facebook_url }}" target="_blank" aria-label="Facebook"
                                class="opacity-80 hover:opacity-100 transition-opacity">
                                <img src="{{ asset('img/icons/facebook.webp') }}" alt="Facebook icon" class="w-7 h-7"
                                    width="28px" height="28px" />
                            </a>
                        @endif
                        @if ($siteSettings?->instagram_url)
                            <a href="{{ $siteSettings->instagram_url }}" target="_blank" rel="noopener noreferrer"
                                aria-label="Instagram" class="opacity-80 hover:opacity-100 transition-opacity">
                                <img src="{{ asset('img/icons/instagram.webp') }}" alt="Instagram icon" class="w-7 h-7"
                                    width="28px" height="28px" />
                            </a>
                        @endif
                        @if ($siteSettings?->youtube_url)
                            <a href="{{ $siteSettings->youtube_url }}" target="_blank" aria-label="YouTube"
                                class="opacity-80 hover:opacity-100 transition-opacity">
                                <img src="{{ asset('img/icons/youtube.webp') }}" alt="YouTube icon" class="w-7 h-7"
                                    width="28px" height="28px" />
                            </a>
                        @endif
                    </div>
                </div>

                {{-- 2-Ustun: Kontaktlar --}}
                <div class="md:col-span-2 footer-anim footer-anim-d2">
                    <div
                        class="grid gap-4 p-5 rounded-xl
                                bg-teal-800/60 backdrop-blur-sm
                                border border-teal-700/40 border-l-4 border-l-teal-400">

                        @php
                            $address = $siteSettings?->address() ?? __('messages.address');
                            $phonePrimary = $siteSettings?->phone_primary;
                            $phoneSecondary = $siteSettings?->phone_secondary;
                            $emailPrimary = $siteSettings?->email_primary;
                            $emailSecondary = $siteSettings?->email_secondary;
                        @endphp

                        <p class="flex items-center gap-3 text-sm lg:text-base text-teal-100">
                            <img src="{{ asset('img/icons/placeholder.webp') }}" alt="Address icon"
                                class="w-6 h-6 invert shrink-0" width="24px" height="24px" />
                            {{ $address }}
                        </p>

                        <p class="flex items-center gap-3 text-sm lg:text-base text-teal-100">
                            <img src="{{ asset('img/icons/003-phone-call.webp') }}" alt="Phone icon"
                                class="w-6 h-6 invert shrink-0" width="24px" height="24px" />
                            {{ __('messages.phone') }}:
                            @if ($phonePrimary)
                                <a href="tel:{{ $phonePrimary }}"
                                    class="text-teal-200 hover:text-white transition-colors">{{ $phonePrimary }}</a>
                                @if ($phoneSecondary)
                                    , <a href="tel:{{ $phoneSecondary }}"
                                        class="text-teal-200 hover:text-white transition-colors">{{ $phoneSecondary }}</a>
                                @endif
                            @else
                                +998 (66) 231-12-53
                            @endif
                        </p>

                        <p class="flex items-center gap-3 text-sm lg:text-base text-teal-100">
                            <img src="{{ asset('img/icons/001-envelope.webp') }}" alt="Email icon"
                                class="w-6 h-6 invert shrink-0" width="24px" height="24px" />
                            Email:
                            @if ($emailPrimary)
                                <span class="obf-email text-teal-200" data-e="{{ base64_encode($emailPrimary) }}"
                                    aria-label="{{ $emailPrimary }}">
                                    <noscript><a href="mailto:{{ $emailPrimary }}"
                                            class="hover:text-white transition-colors">{{ $emailPrimary }}</a></noscript>
                                </span>
                                @if ($emailSecondary)
                                    , <span class="obf-email text-teal-200"
                                        data-e="{{ base64_encode($emailSecondary) }}"
                                        aria-label="{{ $emailSecondary }}">
                                        <noscript><a href="mailto:{{ $emailSecondary }}"
                                                class="hover:text-white transition-colors">{{ $emailSecondary }}</a></noscript>
                                    </span>
                                @endif
                            @else
                                <span class="obf-email text-teal-200" data-e="{{ base64_encode('sies_info@sies.uz') }}"
                                    aria-label="sies_info@sies.uz">
                                    <noscript>sies_info@sies.uz</noscript>
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Pastki qism (Copyright va Ogohlantirish) --}}
            <div class="mt-10 pt-6 border-t border-teal-700/40 text-white footer-anim footer-anim-d3">
                <p class="text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0 opacity-60" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ __('messages.warning') }}
                </p>
                <p class="text-sm mt-1 flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0 opacity-60" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="10" />
                        <path stroke-linecap="round" d="M14.83 14.83a4 4 0 11.01-5.66" />
                    </svg>
                    {{ __('messages.copyright') }}
                </p>
            </div>

        </div>
    </div>
</footer>
