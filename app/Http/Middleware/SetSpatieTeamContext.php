<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;

final class SetSpatieTeamContext
{
    public function handle(Request $request, Closure $next)
    {
        $teamId = $request->user()?->currentTeam?->id; // or null
        app(PermissionRegistrar::class)->setPermissionsTeamId($teamId);

        return $next($request);
    }
}
