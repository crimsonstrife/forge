<?php

namespace App\Providers;

use App\Models\Goal;
use App\Models\Organization;
use App\Models\PermissionSet;
use App\Models\Project;
use App\Models\ProjectRepository;
use App\Models\Sprint;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\GoalPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\ProjectRepositoryPolicy;
use App\Policies\SprintPolicy;
use App\Policies\TeamPolicy;
use App\Policies\TicketPolicy;
use DB;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\PermissionRegistrar;

class AuthServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Team::class => TeamPolicy::class,
        Sprint::class => SprintPolicy::class,
        Goal::class => GoalPolicy::class,
        Organization::class => OrganizationPolicy::class,
        ProjectRepository::class => ProjectRepositoryPolicy::class,
        Ticket::class => TicketPolicy::class
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

        Gate::define(
            'viewApiDocs',
            fn (?User $user) =>
            $user && $user->hasPermissionTo('is-super-admin')
        );

        Gate::define(
            'viewScalar',
            fn (?User $user) =>
            $user && $user->hasPermissionTo('is-super-admin')
        );

        Gate::before(static function (User $user, string $ability) {
            /** @var PermissionRegistrar $reg */
            $reg = app(PermissionRegistrar::class);
            $prev = $reg->getPermissionsTeamId();

            // Temporarily clear team to check global roles/permissions
            $reg->setPermissionsTeamId(null);
            $isSuper = $user->hasPermissionTo('is-super-admin');
            $reg->setPermissionsTeamId($prev);

            // Super admin allow (fast path)
            if ($isSuper) {
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
