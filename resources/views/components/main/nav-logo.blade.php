<a href="/" class="flex w-72 items-center gap-1 space-x-3 md:w-80 rtl:space-x-reverse">
    <img src="{{ $siteSettings?->logoUrl() ?? asset('img/logo.webp') }}" alt="SamISI" width="90px" height="90px" />
    <span class="text-sm font-bold uppercase md:text-base">
        {{ $siteSettings?->siteName() ?? __('messages.app_name') }}
    </span>
</a>
