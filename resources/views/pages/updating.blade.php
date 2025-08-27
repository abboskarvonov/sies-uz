<x-main-layout>
    <x-breadcrumb :menu="$menuModel" :submenu="$submenuModel ?? null" :multimenu="$multimenuModel ?? null" />
    <div class="relative">
        <div
            class="container mx-auto flex flex-col items-center justify-center py-32 text-center bg-gray-100 my-10 rounded-lg shadow dark:bg-gray-700">
            <img src="{{ asset('img/update.webp') }}" alt="Yangilanmoqda" class="mb-8 w-32 md:w-60 dark:invert">
            <h1 class="text-4xl font-extrabold text-gray-800 dark:text-white md:text-5xl">
                {{ __('messages.page_updating') }}
            </h1>
            <p class="mt-6 max-w-2xl text-lg text-gray-600 dark:text-gray-300 mb-6">
                {{ __('messages.page_updating_text') }}
            </p>
            <x-button>
                <a href="{{ url('/') }}">
                    {{ __('messages.back_to_home') }}
                </a>
            </x-button>
        </div>
    </div>
</x-main-layout>
