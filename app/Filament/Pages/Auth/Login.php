<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): void
    {
        $email = strtolower($this->data['email'] ?? '');
        $throttleKey = 'filament-login:' . $email . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'data.email' => __('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => (int) ceil($seconds / 60),
                ]),
            ]);
        }

        try {
            parent::authenticate();
            RateLimiter::clear($throttleKey);
        } catch (\Throwable $e) {
            RateLimiter::hit($throttleKey, 60);
            throw $e;
        }
    }
}
