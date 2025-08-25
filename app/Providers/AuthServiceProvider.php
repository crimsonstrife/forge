<?php

namespace App\Providers;

use App\Models\PermissionSet;
use DB;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->register();
        $this->registerPolicies();

        Gate::before(static function ($user, string $ability) {
            // Super admin: use Spatie's direct check (no Gate recursion)
            if (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo('is-super-admin')) {
                return true;
            }

            // Mutes: compute without calling can()/Gate
            $muted = Cache::remember(
                "auth:user:{$user->getKey()}:muted-perms:v1",
                now()->addMinutes(10),
                static function () use ($user): array {
                    // sets directly on user
                    $setIds = $user->permissionSets()->pluck('permission_sets.id')
                        ->map(fn ($id) => (string) $id)->all();

                    // sets via roles
                    $roleIds = $user->roles()->pluck('roles.id')
                        ->map(fn ($id) => (string) $id)->all();

                    $viaRoleSetIds = $roleIds
                        ? PermissionSet::query()
                            ->whereHas('roles', fn ($q) => $q->whereIn('roles.id', $roleIds))
                            ->pluck('permission_sets.id')->map(fn ($id) => (string) $id)->all()
                        : [];

                    $allSetIds = array_values(array_unique(array_merge($setIds, $viaRoleSetIds)));
                    if (empty($allSetIds)) {
                        return [];
                    }

                    return DB::table('permission_set_mutes')
                        ->join('permissions', 'permissions.id', '=', 'permission_set_mutes.permission_id')
                        ->whereIn('permission_set_mutes.permission_set_id', $allSetIds)
                        ->pluck('permissions.name')
                        ->all();
                }
            );

            if (in_array($ability, $muted, true)) {
                return false; // hard deny
            }

            return null; // continue to normal Gate/Spatie resolution
        });
    }
}
