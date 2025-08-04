<div x-data="{ customModalOpen: false }">
    <!-- Trigger button -->
    <x-icon-button @click="customModalOpen = true">
        <img src="{{ asset('img/icons/007-search.webp') }}" class="w-4 dark:invert" alt="Search icon" />
        <span class="sr-only">Open Search Modal</span>
    </x-icon-button>

    <!-- Modal -->
    <div x-show="customModalOpen" x-transition x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/40" aria-modal="true"
        role="dialog">
        <!-- Modal box -->
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative"
            @keydown.escape.window="customModalOpen = false">
            <!-- Close button -->
            <button @click="customModalOpen = false"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl font-bold focus:outline-none"
                aria-label="Close">
                &times;
            </button>

            <!-- Modal title -->
            <h2 class="text-xl font-semibold mb-4">Custom Modal Title</h2>

            <!-- Modal content -->
            <p class="text-gray-700">
                This is the modal content. The background is blurred.
            </p>

            <!-- Footer -->
            <div class="flex justify-end mt-6">
                <button @click="customModalOpen = false"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
