<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

/**
 * Class Cookies
 * Extends the default Laravel EncryptCookies middleware
 * @package App\Http\Middleware
 */
class Cookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
    ];
}
