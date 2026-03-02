<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

/**
 * Security fix: Host Header Attack (CVSS 5.3)
 *
 * Prevents HTTP Host header injection by whitelisting allowed hostnames.
 * Without this, attackers can inject arbitrary Host values which may be
 * reflected in password-reset links, redirects, or HTML canonical tags.
 */
class TrustHosts extends Middleware
{
    /**
     * Returns patterns for all allowed hostnames.
     * Any request with a Host header not matching these patterns will be rejected.
     */
    public function hosts(): array
    {
        $configured = config('app.trusted_hosts', []);

        return array_filter(array_merge(
            // Auto-derive regex from APP_URL (covers subdomains too)
            [$this->allSubdomainsOfApplicationUrl()],
            // Explicitly allowed hostnames from config
            $configured
        ));
    }
}
