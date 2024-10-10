<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Get the user
        $user = Auth::user();

        // If the user is logged in (make sure $user is not null)
        if ($user) {
            // Ensure the user is an instance of the User model and load the necessary relationships
            if ($user instanceof \App\Models\User) {
                // Eager load the user's roles
                $user->load('roles');

                // Check if the user has the required role
                if (!$user->hasRole($role)) {
                    // Redirect if the user does not have the required role
                    return redirect('/dashboard')->with('error', 'You do not have access to this page.');
                }
            }

            // Continue the request
            return $next($request);
        }

        return $next($request);
    }
}
