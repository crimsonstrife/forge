<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
//use App\Http\Controllers\Controller;
//use App\Models\Projects\Project as ProjectModel;

/**
 * Class Project
 * Middleware for handling project requests
 * @package App\Http\Middleware
 */
class Project
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
