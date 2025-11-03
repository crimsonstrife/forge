<?php

namespace App\Providers;

use App\Domain\Issues\Events\IssueAssigneeChanged;
use App\Listeners\Issues\SendIssueAssignedNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

final class EventServiceProvider extends ServiceProvider
{
    protected static $shouldDiscoverEvents = false;

    /** @var array<class-string, array<int, class-string>> */
    protected $listen = [
        IssueAssigneeChanged::class => [
            SendIssueAssignedNotification::class,
        ],
    ];
}
