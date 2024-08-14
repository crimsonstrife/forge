<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

/**
 * Class CsrfVerification
 * Extends the default Laravel VerifyCsrfToken middleware
 * @package App\Http\Middleware
 */
class CsrfVerification extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
