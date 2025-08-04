<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 h-9 justify-center bg-white border border-gray-300 dark:border-gray-700 shadow dark:bg-gray-900 border rounded-md font-semibold text-xs text-gray-800 dark:text-white uppercase tracking-widest hover:bg-gray-100 dark:hover:bg-gray-800 focus:bg-white dark:focus:bg-gray-900 active:bg-gray-300 dark:active:bg-gray-900 focus:outline-none disabled:opacity-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
