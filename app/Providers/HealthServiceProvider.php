<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\CpuLoadHealthCheck\CpuLoadCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;
use Spatie\SecurityAdvisoriesHealthCheck\SecurityAdvisoriesCheck;

class HealthServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $checks = [
            DatabaseCheck::new()->name('Check Database Connection'),
            CacheCheck::new()
                ->driver((string) config('cache.default'))
                ->name('Cache Check'),
            OptimizedAppCheck::new()->name('Check Optimization'),
            UsedDiskSpaceCheck::new()
                ->name('Check Used Disk Space')
                ->warnWhenUsedSpaceIsAbovePercentage(75)
                ->failWhenUsedSpaceIsAbovePercentage(90),
            ScheduleCheck::new()
                ->name('Check Scheduler')
                ->useCacheStore('file'),
            CpuLoadCheck::new()
                ->failWhenLoadIsHigherInTheLast5Minutes(2.0)
                ->failWhenLoadIsHigherInTheLast15Minutes(1.5),
            SecurityAdvisoriesCheck::new()->name('Check Security Advisories'),
        ];

        if (config('health.queue_check_enabled') && config('queue.default') !== 'sync') {
            $checks[] = QueueCheck::new()
                ->name('Check Job Queue')
                ->useCacheStore('file');
        }

        Health::checks($checks);
    }
}
