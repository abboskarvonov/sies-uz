<!-- Parent x-data -->
<div x-data="{ mobileMenuOpen: false }" class="md:hidden">

    <!-- Hamburger button -->
    <button @click="mobileMenuOpen = !mobileMenuOpen"
        class="p-2 rounded-md text-gray-700 dark:text-gray-300 focus:outline-none focus:ring focus:ring-gray-500">
        <!-- Heroicon Hamburger -->
        <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>

        <!-- Heroicon Close -->
        <svg x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Mobile menu -->
    <div x-show="mobileMenuOpen" x-cloak x-transition
        class="absolute top-full left-0 w-full bg-white dark:bg-gray-900 shadow-lg border-t dark:border-gray-800 z-50">

        @php
            $locale = app()->getLocale();
        @endphp

        <nav class="flex flex-col space-y-1 p-4">
            @foreach ($menus as $menu)
                <a href="{{ localized_page_route($menu) }}"
                    class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-900 dark:text-white font-medium">
                    {{ lc_title($menu) }}
                </a>
            @endforeach
        </nav>
    </div>
</div>
