<?php

namespace App\Services\Socialite;

/**
 * HEMIS talabalar uchun OAuth2 provider.
 * URL: student.sies.uz
 */
class HemisStudentProvider extends HemisAbstractProvider
{
    protected function getBaseUrl(): string
    {
        return 'https://student.sies.uz';
    }
}
