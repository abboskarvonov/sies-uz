<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mening profilim') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- HEMIS ma'lumotlari + bio + rasm --}}
        @livewire('employee-profile')

        <x-section-border />

        {{-- Parol o'zgartirish --}}
        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            <div>
                @livewire('profile.update-password-form')
            </div>
            <x-section-border />
        @endif

        {{-- 2FA --}}
        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            <div>
                @livewire('profile.two-factor-authentication-form')
            </div>
            <x-section-border />
        @endif

        {{-- Boshqa sessiyalarni yopish --}}
        <div>
            @livewire('profile.logout-other-browser-sessions-form')
        </div>

        {{-- Akkauntni o'chirish --}}
        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
            <x-section-border />
            <div>
                @livewire('profile.delete-user-form')
            </div>
        @endif

    </div>
</x-app-layout>
