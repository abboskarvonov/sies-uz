<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['uz', 'ru', 'en'];
    private const DEFAULT_LOCALE = 'uz';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('lang')
            ?? $request->header('Accept-Language')
            ?? self::DEFAULT_LOCALE;

        // Accept-Language headerdan faqat birinchi 2 belgini olish (masalan: "uz-UZ" -> "uz")
        $locale = substr($locale, 0, 2);

        if (!in_array($locale, self::SUPPORTED_LOCALES)) {
            $locale = self::DEFAULT_LOCALE;
        }

        app()->setLocale($locale);

        $response = $next($request);

        $response->headers->set('Content-Language', $locale);

        return $response;
    }
}
