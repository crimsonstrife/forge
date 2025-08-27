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

        // Resolve team id safely: route 'team' (model|string|array) > route 'team_id' > input 'team_id' > currentTeam > null
        $resolveId = static function ($value): ?string {
            if (is_null($value)) {
                return null;
            }
            if (is_object($value)) {
                /** @var mixed $value */
                $id = $value->id ?? null;
                return $id ? (string) $id : null;
            }
            if (is_array($value)) {
                return isset($value['id']) ? (string) $value['id'] : null;
            }
            if (is_string($value) || is_int($value)) {
                return (string) $value;
            }
            return null;
        };

        $teamId =
            $resolveId($request->route('team')) ??
            $resolveId($request->route('team_id')) ??
            $resolveId($request->input('team_id')) ??
            $resolveId(auth()->user()?->currentTeam?->id);

        $registrar->setPermissionsTeamId($teamId);

        return $next($request);
    }
}
