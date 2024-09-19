<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Auth;

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

        // Get the user from the request.
        $user = Auth::user();

        // If the user is logged in (make sure $user is not null)
        if ($user) {
            // Ensure the user is an instance of the User model and load the necessary relationships
            if ($user instanceof \App\Models\User) {
                // Eager load the user's permissions, permission sets, and permission groups
                $user->load('permissions', 'permissionSets.permissions', 'permissionGroups.permissions');

                // Check if the user has any muted permissions
                $mutedPermissions = $user->getMutedPermissions();

                // Log the muted permissions, if any
                if ($mutedPermissions->isNotEmpty()) {
                    logger()->info('Muted permissions: ' . $mutedPermissions->implode(', '));
                }
            } else {
                // Log the error
                logger()->error('User is not an instance of \App\Models\User');
            }

            // Continue the request
            return $next($request);
        }

        // If the user is not authenticated, continue the request
        return $next($request);
    }
}
