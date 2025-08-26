<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\PersonalAccessToken;
use App\Models\Project;
use App\Observers\CommentObserver;
use App\Observers\IssueObserver;
use App\Observers\ProjectObserver;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Laravel\Telescope\TelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (class_exists(TelescopeServiceProvider::class) && $this->app->environment('local')) {
            $this->app->register(TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Issue::observe(IssueObserver::class);
        Project::observe(ProjectObserver::class);
        Comment::observe(CommentObserver::class);
    }
}
