<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\CpuLoadHealthCheck\CpuLoadCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;
use Spatie\SecurityAdvisoriesHealthCheck\SecurityAdvisoriesCheck;

class AppHealthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Health::checks([
            CacheCheck::new()
                ->everyFifteenMinutes()
                ->name('Cache Check'),
            OptimizedAppCheck::new()
                ->everyThirtyMinutes()
                ->name('Check Optimization'),
            UsedDiskSpaceCheck::new()
                ->daily()
                ->name('Check Used DiskSpace'),
            DatabaseCheck::new()
                ->name('Check Database Connection')
                ->everyMinute(),
            QueueCheck::new()
                ->everyFiveMinutes()
                ->name('Check Job Queue'),
            CpuLoadCheck::new()
                ->failWhenLoadIsHigherInTheLast5Minutes(2.0)
                ->failWhenLoadIsHigherInTheLast15Minutes(1.5),
            SecurityAdvisoriesCheck::new()
                ->daily(),
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
