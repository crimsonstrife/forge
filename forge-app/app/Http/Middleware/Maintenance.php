<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class Maintenance extends Middleware
{
    /**
     * The URIs that should be accessible while the application is in maintenance mode.
     *
     * @var array<int, string>
     */
    protected $except = [
        'maintenance',
        'admin/*',
    ];
}
