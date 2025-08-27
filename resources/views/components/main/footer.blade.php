<div class="w-full bg-gray-200 dark:bg-gray-950">
    <div class="container mx-auto py-10 lg:py-20">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3 md:gap-5 lg:gap-10">
            {{-- Logo va nom --}}
            <div class="grid justify-center justify-items-center gap-2">
                <img src="/img/logo.webp" alt="SamISI logo" class="w-auto max-w-[160px]" />
                <h1 class="text-center text-lg font-bold uppercase lg:text-xl">
                    {{ __('messages.app_name') }}
                </h1>
            </div>

            {{-- Kontaktlar --}}
            <div class="grid content-start gap-6">
                <p class="flex items-center gap-2 text-sm lg:text-base">
                    <img src="/img/icons/placeholder.webp" class="w-5 dark:invert lg:w-7" alt="Manzil belgisi" />
                    {{ __('messages.address') }}
                </p>
                <p class="flex items-center gap-2 text-sm lg:text-base">
                    <img src="/img/icons/003-phone-call.webp" class="w-5 dark:invert lg:w-7" alt="Telefon belgisi" />
                    {{ __('messages.phone') }}: +998 (66) 231-12-53, +998 (66) 231-03-93
                </p>
                <p class="flex items-center gap-2 text-sm lg:text-base">
                    <img src="/img/icons/001-envelope.webp" class="w-5 dark:invert lg:w-7" alt="Email belgisi" />
                    Email: sies_info@edu.uz, samisi@exat.uz
                </p>

                {{-- Ijtimoiy tarmoqlar --}}
                <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                    <a href="https://t.me/example" target="_blank" rel="noopener noreferrer">
                        <img src="/img/icons/telegram.webp" class="w-8" alt="Telegram" />
                    </a>
                    <a href="https://facebook.com/example" target="_blank" rel="noopener noreferrer">
                        <img src="/img/icons/facebook.webp" class="w-8" alt="Facebook" />
                    </a>
                    <a href="https://instagram.com/example" target="_blank" rel="noopener noreferrer">
                        <img src="/img/icons/instagram.webp" class="w-8" alt="Instagram" />
                    </a>
                    <a href="https://youtube.com/example" target="_blank" rel="noopener noreferrer">
                        <img src="/img/icons/youtube.webp" class="w-8" alt="YouTube" />
                    </a>
                </div>
            </div>

            {{-- Google Map --}}
            <div class="w-full">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3602.795078633065!2d66.95965593203682!3d39.652679673533754!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3f4d18d09d2a9295%3A0x5642746f9fcbed8!2sSamarkand%20State%20Institute%20of%20Economics%20and%20Service!5e0!3m2!1sen!2s!4v1710216853970!5m2!1sen!2s"
                    width="100%" height="250" title="SamISI Location" loading="lazy" allowfullscreen></iframe>
            </div>
        </div>

        {{-- Pastki qism --}}
        <div class="mt-5 grid gap-5 border-t-2 border-t-gray-700 pt-5 dark:border-t-gray-200">
            <p class="flex items-center gap-2 text-sm">
                <img src="/img/icons/alert.webp" class="w-4 dark:invert lg:w-5" alt="Ogohlantirish" />
                {{ __('messages.warning') }}
            </p>
            <p class="flex items-center gap-2 text-sm">
                <img src="/img/icons/copyright.webp" class="w-4 dark:invert lg:w-5" alt="Copyright belgisi" />
                {{ __('messages.copyright') }}
            </p>
        </div>
    </div>
</div>
