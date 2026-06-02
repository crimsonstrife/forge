<?php

namespace App\Providers;

use App\Integrations\Sentry\Observers\SentryCommentObserver;
use App\Integrations\Sentry\Observers\SentryIssueObserver;
use App\Models\Comment;
use App\Models\Issue;
use Illuminate\Support\ServiceProvider;

class SentryIntegrationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Issue::observe(SentryIssueObserver::class);
        Comment::observe(SentryCommentObserver::class);
    }
}
