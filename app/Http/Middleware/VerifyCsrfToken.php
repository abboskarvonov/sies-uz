<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [];

    protected function tokensMatch($request): bool
    {
        $match = parent::tokensMatch($request);

        if (! $match) {
            Log::warning('CSRF token mismatch', [
                'url'            => $request->fullUrl(),
                'method'         => $request->method(),
                'session_id'     => $request->hasSession() ? $request->session()->getId() : 'NO_SESSION',
                'session_token'  => $request->hasSession() ? $request->session()->token() : 'NO_SESSION',
                'x_csrf_token'   => $request->header('X-CSRF-TOKEN', 'MISSING'),
                'input_token'    => $request->input('_token', 'MISSING'),
                'x_xsrf_token'   => $request->header('X-XSRF-TOKEN', 'MISSING'),
                'session_keys'   => $request->hasSession() ? array_keys($request->session()->all()) : [],
                'has_session'    => $request->hasSession(),
                'cookies'        => array_keys($request->cookies->all()),
            ]);
        }

        return $match;
    }
}
