<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\Auth\PermissionGroup;
use App\Models\Auth\PermissionSet;
use App\Models\Auth\Role;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\PassportServiceProvider;
use Laravel\Sanctum\SanctumServiceProvider;
use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;
use App\Models\Projects\Project;
use App\Models\User;
use App\Policies\ActivityPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\PermissionSetPolicy;
use App\Policies\PermissionGroupPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\UserPolicy;
use App\Policies\RolePolicy;
use Spatie\Permission\Models\Permission;

/**
 * AuthServerProvider class.
 *
 * This class is responsible for providing authentication server functionality.
 * It extends the ServiceProvider class and registers services required for authentication.
 * It also sets the expiration time for access tokens and refresh tokens using Passport.
 * Additionally, it enables the implicit grant and sets the personal access token model using Sanctum.
 */
class AuthServerProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        PermissionSet::class => PermissionSetPolicy::class,
        PermissionGroup::class => PermissionGroupPolicy::class,
        Project::class => ProjectPolicy::class,
        Activity::class => ActivityPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->register(PassportServiceProvider::class);
        $this->app->register(SanctumServiceProvider::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->register();
        $this->registerPolicies();

        //Passport::routes();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        Passport::enableImplicitGrant();
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
