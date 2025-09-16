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
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);

        $resolveId = static function ($value): ?string {
            if (is_string($value)) {
                return $value;
            }
            if (is_object($value)) {
                // Try common accessors/attributes
                if (method_exists($value, 'getKey')) {
                    return (string) $value->getKey();
                }
                if (property_exists($value, 'id')) {
                    return (string) $value->id;
                }
            }
            return null;
        };

        $resolveTeamFromModel = static function ($value): ?string {
            if (is_object($value)) {
                if (method_exists($value, 'getAttribute')) {
                    $id = $value->getAttribute('team_id');
                    return $id ? (string) $id : null;
                }
                if (property_exists($value, 'team_id') && $value->team_id) {
                    return (string) $value->team_id;
                }
            }
            return null;
        };

        // Prefer explicit team indicators or resource-owned team.
        $teamId =
            $resolveId($request->route('team'))
            ?? $resolveId($request->route('team_id'))
            ?? $resolveTeamFromModel($request->route('project'))
            ?? $resolveTeamFromModel($request->route('issue'))
            ?? $resolveId($request->input('team_id'))
            ?? null;

        $registrar->setPermissionsTeamId($teamId);

        return $next($request);
    }
}
