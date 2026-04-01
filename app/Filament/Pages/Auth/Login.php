<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
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
            $response = parent::authenticate();
            RateLimiter::clear($throttleKey);
            return $response;
        } catch (\Throwable $e) {
            RateLimiter::hit($throttleKey, 60);
            throw $e;
        }
    }
}
