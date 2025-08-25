<?php

namespace App\Providers;

use App\Models\PermissionSet;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
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
            if ($user->can('is-super-admin')) {
                return true;
            }

            // Cached muted permission names
            $muted = cache()->remember(
                "auth:user:{$user->getKey()}:muted-perms:v1",
                now()->addMinutes(10),
                static function () use ($user): array {
                    /** @var PermissionSet $set */
                    $sets = $user->permissionSets()
                        ->with('mutedPermissions:id,name,guard_name')
                        ->get();

                    // Also include mutes from sets attached to the user's roles
                    $roleMutes = PermissionSet::query()
                        ->whereHas('roles', fn ($q) => $q->whereIn('id', $user->roles->pluck('id')))
                        ->with('mutedPermissions:id,name,guard_name')
                        ->get();

                    return collect([$sets, $roleMutes])
                        ->flatten()
                        ->pluck('mutedPermissions')
                        ->flatten()
                        ->pluck('name')
                        ->unique()
                        ->values()
                        ->all();
                }
            );

            if (in_array($ability, $muted, true)) {
                return false;
            }

            return null; // let Spatie resolve grants
        });
    }
}
