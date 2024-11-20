<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware to check if the user has permission to access the project.
 *
 * This middleware ensures that the authenticated user has the necessary
 * permissions to access the requested project. If the user does not have
 * the required permissions, they will be redirected or an error response
 * will be returned.
 *
 * @package App\Http\Middleware
 */
class CheckProjectPermission
{
    /**
     * Handle an incoming request and check for the specified project permission.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $requiredPermission
     * @return mixed
     */
    public function handle($request, Closure $next, $requiredPermission)
    {
        $user = Auth::user();

        // Get the project ID from the route or request
        $projectId = $request->route('project') ?? $request->input('project_id');

        if (!$user || !$user->hasProjectPermission($projectId, $requiredPermission)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
