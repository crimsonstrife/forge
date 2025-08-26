<?php

namespace App\Providers;

use App\Models\PermissionSet;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Policies\ProjectPolicy;
use App\Policies\TeamPolicy;
use DB;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Team::class => TeamPolicy::class,
    ];

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

        Gate::before(static function (User $user, string $ability) {
            // Super admin allow (fast path)
            if ($user->hasPermissionTo('is-super-admin')) {
                return true; // allow everything
            }

            // Only evaluate mutes for *permission-like* abilities (contain a dot).
            // Policy abilities like 'view', 'create', 'update' should fall through.
            if (! str_contains($ability, '.')) {
                return null; // defer to normal Gate / policies
            }

            // Compute muted permissions (cache OK)
            $muted = Cache::remember(
                "auth:user:{$user->getKey()}:muted-perms:v1",
                now()->addMinutes(10),
                static function () use ($user): array {
                    // sets directly on user
                    $userSetIds = method_exists($user, 'permissionSets')
                        ? $user->permissionSets()->pluck('permission_sets.id')->all()
                        : [];

                    // sets via the user's roles
                    $roleIds = $user->roles()->pluck('roles.id')->all();
                    $roleSetIds = empty($roleIds)
                        ? []
                        : PermissionSet::query()
                            ->whereHas('roles', fn ($q) => $q->whereIn('roles.id', $roleIds))
                            ->pluck('permission_sets.id')->all();

                    $allSetIds = array_values(array_unique(array_map('strval', array_merge($userSetIds, $roleSetIds))));
                    if (empty($allSetIds)) {
                        return [];
                    }

                    return DB::table('permission_set_mutes as m')
                        ->join('permissions as p', 'p.id', '=', 'm.permission_id')
                        ->whereIn('m.permission_set_id', $allSetIds)
                        ->pluck('p.name')
                        ->all();
                }
            );

            // If this specific permission is muted, hard-deny
            if (in_array($ability, $muted, true)) {
                return false; // deny the ability (e.g., 'issues.update', etc.)
            }

            return null; // defer to Spatie / policies
        });
    }
}
