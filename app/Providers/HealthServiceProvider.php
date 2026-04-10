<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

class HealthServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $checks = [
            DatabaseCheck::new(),
            CacheCheck::new(),
            EnvironmentCheck::new(),
            DebugModeCheck::new(),
            UsedDiskSpaceCheck::new()
                ->warnWhenUsedSpaceIsAbovePercentage(75)
                ->failWhenUsedSpaceIsAbovePercentage(90),

            // Only add if Redis is configured in your app.
            // RedisCheck::new()->if(fn () => config('database.redis.default.host') !== null),

            // Optional but useful if you rely on the scheduler.
            ScheduleCheck::new(),
        ];

        // Avoid serializing a deferred run-condition closure into HealthQueueJob.
        if (config('queue.default') !== 'sync') {
            $checks[] = QueueCheck::new();
        }

        Health::checks($checks);
    }
}
