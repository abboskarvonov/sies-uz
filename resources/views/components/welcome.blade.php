<div
    class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
    <x-application-logo class="block h-12 w-auto" />

    <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
        Welcome to your Dashboard!
    </h1>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4">
            @php($u = Auth::user())
            <div class="flex items-center gap-4">
                <div
                    class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 to-blue-500 text-white flex items-center justify-center font-semibold">
                    {{ Str::of($u->name ?? 'U')->substr(0, 1)->upper() }}
                </div>
                <div>
                    <div class="text-lg font-semibold">
                        {{ $u->name ?? '—' }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        {{ $u->email ?? '—' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
