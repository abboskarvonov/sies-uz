<?php

namespace App\Services\Socialite;

/**
 * HEMIS xodimlar uchun OAuth2 provider.
 * URL: hemis.sies.uz
 */
class HemisEmployeeProvider extends HemisAbstractProvider
{
    protected function getBaseUrl(): string
    {
        return 'https://hemis.sies.uz';
    }
}
