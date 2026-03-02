<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security fix: Missing HTTP security headers (Acunetix findings)
 *
 * Adds the following headers to every response:
 *  - X-Frame-Options: prevents clickjacking
 *  - X-Content-Type-Options: prevents MIME-type sniffing
 *  - Strict-Transport-Security: enforces HTTPS (HSTS)
 *  - Permissions-Policy: restricts browser feature access
 *  - Referrer-Policy: limits referrer information leakage
 *  - Content-Security-Policy-Report-Only: CSP in report-only mode
 *    (switch to Content-Security-Policy once violations are reviewed)
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking: only allow framing from same origin
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent browsers from MIME-sniffing a response away from declared Content-Type
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // HSTS: tell browsers to always use HTTPS for this domain (only send over HTTPS)
        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        // Restrict access to sensitive browser APIs
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=()'
        );

        // Control how much referrer info is sent to external sites
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // CSP in report-only mode — monitors violations without blocking.
        // Review violations in browser console, then switch header name to
        // "Content-Security-Policy" once the policy is confirmed correct.
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com https://www.google-analytics.com",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: blob: https: http://i.ytimg.com https://i.ytimg.com",
            "font-src 'self' data:",
            "frame-src 'self' https://www.youtube.com https://maps.google.com https://www.google.com",
            "connect-src 'self' https://www.google-analytics.com https://analytics.google.com",
            "media-src 'self'",
        ]);
        $response->headers->set('Content-Security-Policy-Report-Only', $csp);

        return $response;
    }
}
