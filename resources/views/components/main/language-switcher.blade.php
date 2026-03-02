@php
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

    $supportedLocales = LaravelLocalization::getSupportedLocales();
    $currentLocale = LaravelLocalization::getCurrentLocale();
@endphp

<div x-data="{ open: false }" class="relative inline-block text-left" @click.away="open = false">
    <x-icon-button @click="open = !open">
        <img src="{{ asset('img/flags/' . $currentLocale . '.png') }}" alt="{{ $currentLocale }}" class="w-5 h-auto">
    </x-icon-button>

    <div x-show="open" x-cloak
        class="absolute z-50 mt-2 w-36 rounded-xl overflow-hidden
               bg-teal-800/85 backdrop-blur-xl
               border border-teal-700/40"
        style="box-shadow: 0 10px 40px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.06);">
        <div class="py-1">
            @foreach ($supportedLocales as $localeCode => $properties)
                <a href="{{ localized_url($localeCode) }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-teal-100 hover:bg-teal-700/60 hover:text-white transition-colors">
                    <img src="{{ asset('img/flags/' . $localeCode . '.png') }}" alt="{{ $localeCode }}"
                        class="w-5 h-auto">
                    {{ $properties['native'] }}
                    @if ($localeCode === $currentLocale)
                        <span class="ml-auto text-teal-300">✓</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
