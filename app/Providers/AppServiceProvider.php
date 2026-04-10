<?php

namespace App\Providers;

use App\Contracts\RepositoryProviderInterface;
use App\Models\Comment;
use App\Models\Issue;
use App\Models\PermissionSet;
use App\Models\PersonalAccessToken;
use App\Models\Project;
use App\Models\Role;
use App\Session\ResilientSessionManager;
use App\Observers\CommentObserver;
use App\Observers\IssueObserver;
use App\Observers\PermissionSetObserver;
use App\Observers\ProjectObserver;
use App\Observers\RoleObserver;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Laravel\Telescope\TelescopeServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (class_exists(TelescopeServiceProvider::class) && $this->app->environment('local')) {
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->singleton(ResilientSessionManager::class, function ($app) {
            return new ResilientSessionManager($app);
        });

        $this->app->singleton(SessionManager::class, function ($app) {
            return $app->make(ResilientSessionManager::class);
        });

        $this->app->singleton('session', function ($app) {
            return $app->make(ResilientSessionManager::class);
        });

        $this->app->singleton('session.store', function ($app) {
            return $app->make('session')->driver();
        });

        $this->app->singleton(StartSession::class, function ($app) {
            return new StartSession($app->make(SessionManager::class), function () use ($app) {
                return $app->make(CacheFactory::class);
            });
        });

        $this->app->bind(RepositoryProviderInterface::class, GitHubRepositoryProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return; // do not touch URL/Request during composer/CLI
        }

        Scramble::configure()->withDocumentTransformers(function (OpenApi $doc) {
            $doc->info->title = config('app.name').' API';
            $doc->secure(SecurityScheme::http('bearer')); // default for all endpoints
        });

        RateLimiter::for('api', static function (Request $request) {
            $key = optional($request->user())?->getAuthIdentifier()
                ?? optional($request->user())?->currentAccessToken()?->id
                ?? $request->ip();

            return Limit::perMinute(120)->by($key);
        });

        RateLimiter::for('ticket-ingest', static function (Request $request) {
            $key = $request->attributes->get('ingest_key');
            $bucket = $key?->id ?? $request->ip();

            return [Limit::perMinute(20)->by('ingest:' . $bucket)];
        });

        Paginator::useBootstrapFive();
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Issue::observe(IssueObserver::class);
        Project::observe(ProjectObserver::class);
        Comment::observe(CommentObserver::class);
        Role::observe(RoleObserver::class);
        PermissionSet::observe(PermissionSetObserver::class);

        Event::listen(static function (SocialiteWasCalled $event) {
            $event->extendSocialite('github', \SocialiteProviders\GitHub\Provider::class);
            $event->extendSocialite('gitea', \SocialiteProviders\Gitea\Provider::class);
            $event->extendSocialite('gitlab', \SocialiteProviders\GitLab\Provider::class);
            $event->extendSocialite('discord', \SocialiteProviders\Discord\Provider::class);
            $event->extendSocialite('todoist', \SocialiteProviders\Todoist\Provider::class);
            $event->extendSocialite('atlassian', \SocialiteProviders\Atlassian\Provider::class);
        });
    }
}
