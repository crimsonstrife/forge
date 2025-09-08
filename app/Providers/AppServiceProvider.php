<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\PermissionSet;
use App\Models\PersonalAccessToken;
use App\Models\Project;
use App\Models\Role;
use App\Observers\CommentObserver;
use App\Observers\IssueObserver;
use App\Observers\PermissionSetObserver;
use App\Observers\ProjectObserver;
use App\Observers\RoleObserver;
use Illuminate\Support\Facades\Event;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return; // do not touch URL/Request during composer/CLI
        }

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
        });
    }
}
