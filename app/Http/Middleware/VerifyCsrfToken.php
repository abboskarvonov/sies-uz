<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Livewire file upload uses signed URLs (abort_unless hasValidSignature)
        // which already prevents forgery — CSRF is redundant here.
        'livewire/upload-file',
    ];
}
