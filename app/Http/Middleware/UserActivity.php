<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {
            $cacheKey = 'user-activity-' . Auth::id();

            if (!Cache::has($cacheKey)) {
                User::where('id', Auth::id())->update(['last_seen_at' => now()]);
                Cache::put($cacheKey, true, now()->addMinutes(5));
            }
        }

        return $next($request);
    }
}
