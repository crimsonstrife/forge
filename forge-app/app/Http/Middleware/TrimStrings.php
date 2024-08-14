<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

/**
 * Class TrimStrings
 * Extends the default Laravel TrimStrings middleware
 * @package App\Http\Middleware
 */
class TrimStrings extends Middleware
{
    /**
     * The attributes that should not be trimmed.
     *
     * @var array
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}
