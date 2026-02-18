<footer class="w-full bg-white dark:bg-gray-800 border-t-8 border-gray-900 dark:border-gray-700">

    {{-- Katta Xarita Qismi (Eng Yuqorida) - IntersectionObserver bilan lazy-load --}}
    <div class="w-full h-[450px] overflow-hidden bg-gray-200 dark:bg-gray-700" id="map-container"
         data-map-src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3602.795078633065!2d66.95965593203682!3d39.652679673533754!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3f4d18d09d2a9295%3A0x5642746f9fcbed8!2sSamarkand%20State%20Institute%20of%20Economics%20and%20Service!5e0!3m2!1sen!2s!4v1710216853970!5m2!1sen!2s">
        <div class="flex items-center justify-center h-full text-gray-400">
            <svg class="w-12 h-12 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
    </div>

    <div class="w-full bg-gray-200 dark:bg-gray-950">
        <div class="container mx-auto px-4 lg:px-0 py-12 lg:py-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">

                {{-- 1-Ustun: Logo va Ijtimoiy Tarmoqlar --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <img src="/img/logo.webp" alt="SamISI logo" class="w-auto max-w-[160px]" />
                        <h1 class="text-center text-lg font-bold uppercase lg:text-xl">
                            {{ __('messages.app_name') }}
                        </h1>
                    </div>

                    {{-- Ijtimoiy tarmoqlar (yumaloq) --}}
                    <div class="flex space-x-3 pt-4">
                        <a href="https://t.me/example" target="_blank" rel="noopener noreferrer">
                            <img src="/img/icons/telegram.webp" class="w-7" alt="Telegram" />
                        </a>
                        <a href="https://facebook.com/example" target="_blank" rel="noopener noreferrer">
                            <img src="/img/icons/facebook.webp" class="w-7" alt="Facebook" />
                        </a>
                        <a href="https://instagram.com/example" target="_blank" rel="noopener noreferrer">
                            <img src="/img/icons/instagram.webp" class="w-7" alt="Instagram" />
                        </a>
                        <a href="https://youtube.com/example" target="_blank" rel="noopener noreferrer">
                            <img src="/img/icons/youtube.webp" class="w-7" alt="YouTube" />
                        </a>
                    </div>
                </div>

                {{-- 2-Ustun: Borderli Kontaktlar --}}
                <div class="md:col-span-2">
                    <div
                        class="grid gap-4 p-5 bg-white dark:bg-gray-700 rounded-xl shadow-inner border-l-4 border-gray-600 dark:border-gray-400">

                        <p class="flex items-center gap-3 text-sm lg:text-base text-gray-700 dark:text-gray-300">
                            <img src="/img/icons/placeholder.webp" class="w-6 dark:invert" alt="Manzil belgisi" />
                            {{ __('messages.address') }}
                        </p>
                        <p class="flex items-center gap-3 text-sm lg:text-base text-gray-700 dark:text-gray-300">
                            <img src="/img/icons/003-phone-call.webp" class="w-6 dark:invert" alt="Telefon belgisi" />
                            {{ __('messages.phone') }}: +998 (66) 231-12-53
                        </p>
                        <p class="flex items-center gap-3 text-sm lg:text-base text-gray-700 dark:text-gray-300">
                            <img src="/img/icons/001-envelope.webp" class="w-6 dark:invert" alt="Email belgisi" />
                            Email: sies_info@sies.uz, samisi@exat.uz
                        </p>
                    </div>
                </div>
            </div>

            {{-- Pastki qism (Copyright va Ogohlantirish) --}}
            <div class="mt-10 pt-6 border-t border-gray-400 dark:border-gray-600 text-gray-600 dark:text-gray-400">
                <p class="text-sm">
                    <img src="/img/icons/alert.webp" class="w-4 inline dark:invert mr-2" alt="Ogohlantirish" />
                    {{ __('messages.warning') }}
                </p>
                <p class="text-sm mt-1">
                    <img src="/img/icons/copyright.webp" class="w-4 inline dark:invert mr-2" alt="Copyright belgisi" />
                    {{ __('messages.copyright') }}
                </p>
            </div>
        </div>
    </div>
</footer>
