<!-- Dark Mode Toggle Button -->
<x-icon-button id="darkModeToggle" white>
    <span id="darkIcon" class="hidden">
        <img src="{{ asset('img/icons/009-moon.webp') }}" alt="Icon" class="w-4">
    </span>
    <span id="lightIcon" class="hidden"><img src="{{ asset('img/icons/008-sun.webp') }}" alt="Icon"
            class="w-4 dark:invert"></span>
</x-icon-button>

@push('scripts')
    <script>
        const toggle = document.getElementById('darkModeToggle');
        const html = document.documentElement;
        const darkIcon = document.getElementById('darkIcon');
        const lightIcon = document.getElementById('lightIcon');

        // Load from localStorage
        if (localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
            darkIcon.classList.add('hidden');
            lightIcon.classList.remove('hidden');
        } else {
            html.classList.remove('dark');
            darkIcon.classList.remove('hidden');
            lightIcon.classList.add('hidden');
        }

        // Toggle click
        toggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            if (html.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                darkIcon.classList.add('hidden');
                lightIcon.classList.remove('hidden');
            } else {
                localStorage.setItem('theme', 'light');
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
            }
        });
    </script>
@endpush
