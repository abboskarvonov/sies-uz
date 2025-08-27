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
        class="absolute z-50 mt-2 w-36 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
        <div class="py-1">
            @foreach ($supportedLocales as $localeCode => $properties)
                <a href="{{ localized_url($localeCode) }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600">
                    <img src="{{ asset('img/flags/' . $localeCode . '.png') }}" alt="{{ $localeCode }}"
                        class="w-5 h-auto">
                    {{ $properties['native'] }}
                    @if ($localeCode === $currentLocale)
                        <span class="ml-auto text-green-500">✓</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
