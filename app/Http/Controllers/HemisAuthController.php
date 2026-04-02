<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\User;
use App\Services\HemisPositionSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class HemisAuthController extends Controller
{
    // ─── Employee ────────────────────────────────────────────────────

    public function redirectEmployee(): RedirectResponse
    {
        return Socialite::driver('hemis-employee')->redirect();
    }

    public function callbackEmployee(): RedirectResponse
    {
        try {
            $hemisUser = Socialite::driver('hemis-employee')->user();
        } catch (Throwable $e) {
            Log::error('HEMIS employee OAuth error', ['error' => $e->getMessage()]);
            return redirect()->route('login')
                ->withErrors(['hemis' => "HEMIS orqali kirishda xatolik yuz berdi. Qaytadan urinib ko'ring."]);
        }

        $user = $this->findOrCreateUser($hemisUser, 'employee');

        // Login dan keyin HEMIS API dan lavozimlarini sync qilish
        $hemisLogin = $hemisUser->getRaw()['login'] ?? null;
        $hemisUuid  = $hemisUser->getRaw()['uuid']  ?? null;
        app(HemisPositionSyncService::class)->sync($user, $hemisLogin, $hemisUuid);

        Auth::login($user, remember: true);

        return redirect()->intended(route('employee.profile'));
    }

    // ─── Student ─────────────────────────────────────────────────────

    public function redirectStudent(): RedirectResponse
    {
        return Socialite::driver('hemis-student')->redirect();
    }

    public function callbackStudent(): RedirectResponse
    {
        try {
            $hemisUser = Socialite::driver('hemis-student')->user();
        } catch (Throwable $e) {
            Log::error('HEMIS student OAuth error', ['error' => $e->getMessage()]);
            return redirect()->route('login')
                ->withErrors(['hemis' => "HEMIS orqali kirishda xatolik yuz berdi. Qaytadan urinib ko'ring."]);
        }

        $user = $this->findOrCreateUser($hemisUser, 'student');

        Auth::login($user, remember: true);

        return redirect()->intended('/');
    }

    // ─── Shared: user topish / yaratish ──────────────────────────────

    private function findOrCreateUser($hemisUser, string $type): User
    {
        $hemisId    = (string) $hemisUser->getId();
        $hemisUuid  = $hemisUser->getRaw()['uuid'] ?? null;
        $hemisEmail = $hemisUser->getEmail() ?: null;
        $hemisName  = $hemisUser->getName();

        // 1. hemis_id bo'yicha qidirish
        $user = User::where('hemis_id', $hemisId)->first();

        // 2. Topilmasa — email bo'yicha qidirish
        if (! $user && $hemisEmail) {
            $user = User::where('email', $hemisEmail)->first();
        }

        if ($user) {
            $user->hemis_id   = $hemisId;
            $user->hemis_uuid = $hemisUuid ?? $user->hemis_uuid;
            $user->hemis_type = $type;
            $user->name       = $hemisName ?? $user->name;

            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
            }

            $user->save();
            return $user;
        }

        // 3. Yangi foydalanuvchi yaratish
        return User::create([
            'hemis_id'           => $hemisId,
            'hemis_uuid'         => $hemisUuid,
            'hemis_type'         => $type,
            'name'               => $hemisName ?? 'HEMIS User',
            'email'              => $hemisEmail,
            'password'           => null,
            'profile_photo_path' => null,
            'email_verified_at'  => now(),
        ]);
    }
}
