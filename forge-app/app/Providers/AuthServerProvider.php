<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
//use Illuminate\Support\Facades\Gate;
use Laravel\Passport\PassportServiceProvider;
use Laravel\Sanctum\Sanctum;

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
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->register(PassportServiceProvider::class);
        $this->app->register(\Laravel\Sanctum\SanctumServiceProvider::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->register();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        Passport::enableImplicitGrant();
        Sanctum::usePersonalAccessTokenModel(\App\Models\PersonalAccessToken::class);
    }
}
