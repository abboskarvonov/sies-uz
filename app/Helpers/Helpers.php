<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;



if (!function_exists('authUser')) {
    /**
     * Authenticated user with type hint.
     *
     * @return User|null
     */
    function authUser(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user;
    }
}