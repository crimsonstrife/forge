<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to ensure that the application is running in mock mode.
 *
 * This middleware checks if the application is in a mock mode environment
 * and performs necessary actions based on that.
 */
class EnsureMockMode
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /**
         * Checks if the application is running in production environment and mock mode is enabled.
         * If both conditions are true, aborts the request with a 403 status code and an error message.
         *
         * @throws \Symfony\Component\HttpKernel\Exception\HttpException If mock mode is enabled in production.
         */
        if (app()->environment('production') && config('services.mock_mode')) {
            abort(403, 'Mock mode is not allowed in production.');
        }

        return $next($request);
    }
}
