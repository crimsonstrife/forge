<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class SetPermissionsTeamContext
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);

        // Priority: route param > explicitly provided > current team > null
        $teamId = $request->route('team')?->id
            ?? $request->route('team_id')
            ?? $request->input('team_id')
            ?? auth()->user()?->currentTeam?->id;

        $registrar->setPermissionsTeamId($teamId);

        return $next($request);
    }
}
