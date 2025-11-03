<?php

namespace App\Providers;

use App\Models\Issue;
use App\Observers\IssueObserver;
use Illuminate\Support\ServiceProvider;

class ModelObserverServiceProvider extends ServiceProvider
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
        Issue::observe(IssueObserver::class);
    }
}
