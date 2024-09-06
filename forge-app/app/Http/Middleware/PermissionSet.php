<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\PermissionRegistrar;

class PermissionSet
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // This middleware is used to set the permissions using the permission, permissionSets, or permissionSetGroups.
        // It also determines if any permissions are being muted for the User by a permissionSet.
        $registrar = app(PermissionRegistrar::class);

        // Get the current User if they are logged in, set the permissions for the User.
        if (!empty($user = auth()->user)) {
            //TODO: Set the permissions for the User.

            //When the permissions are set, return the request.
            return $next($request);
        }

        // If the User is not logged in, return the request.
        return $next($request);
    }
}
