<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware to check if the user has permission to access the team.
 *
 * This middleware ensures that the authenticated user has the necessary
 * permissions to access the requested team. If the user does not have
 * the required permissions, they will be redirected or an error response
 * will be returned.
 *
 * @package App\Http\Middleware
 */
class CheckTeamPermission
{
    /**
     * Handle an incoming request and check for the specified team permission.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $user = Auth::user();
        $teamId = $request->route('team') ?? $request->input('team_id');

        if (!$user || !$user->hasTeamPermission($teamId, $permission)) {
            abort(403, 'You do not have the required permission.');
        }

        return $next($request);
    }
}
