<?php

use App\Console\Commands\CheckForAppUpdate;
use App\Console\Commands\DeleteExpiredBans;
use App\Console\Commands\RecalcIssueRollups;
use App\Console\Commands\ReverbHealthCheck;
use App\Console\Commands\SendIssueNotificationDigests;
use App\Console\Commands\SyncRepositoryIssues;
use App\Jobs\BuildProjectDailyReportsJob;
use App\Jobs\BuildSprintDailyReportsJob;
use App\Jobs\RefreshOpenIssueAgesJob;
use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

if (config('ban.automated_cleanup_enabled', true)) {
    $scheduledExpiredBanCleanup = Schedule::command(DeleteExpiredBans::class);
    $periodicity = config('ban.automated_cleanup_periodicity', 'everyMinute');

    if (is_string($periodicity) && method_exists($scheduledExpiredBanCleanup, $periodicity)) {
        $scheduledExpiredBanCleanup->{$periodicity}();
    } else {
        $scheduledExpiredBanCleanup->everyMinute();
    }

    $scheduledExpiredBanCleanup->withoutOverlapping(10);
}

Schedule::command(CheckForAppUpdate::class)
    ->dailyAt('09:00');

Schedule::command(SendIssueNotificationDigests::class)
    ->dailyAt('08:00');

Schedule::command(SyncRepositoryIssues::class, ['--all'])
    ->everyFifteenMinutes()
    ->withoutOverlapping(30);

Schedule::command(RecalcIssueRollups::class)
    ->everyFiveMinutes();

if (config('health.reverb_healthcheck_enabled')) {
    Schedule::command(ReverbHealthCheck::class)
        ->everyFiveMinutes()
        ->withoutOverlapping(10);
}

// Record the scheduler heartbeat before running health checks that depend on it.
Schedule::command(ScheduleCheckHeartbeatCommand::class)
    ->everyMinute()
    ->withoutOverlapping(5);

Schedule::command(RunHealthChecksCommand::class)
    ->everyMinute()
    ->withoutOverlapping(10);

Schedule::call(static function (): void {
    $yesterday = Carbon::yesterday();
    Project::query()
        ->select('id')
        ->chunkById(200, static function ($projects) use ($yesterday): void {
            foreach ($projects as $p) {
                dispatch(new BuildProjectDailyReportsJob($p->id, $yesterday));
            }
        });
})->dailyAt('01:15');

Schedule::call(static function (): void {
    $yesterday = Carbon::yesterday();
    Sprint::query()
        ->select(['id', 'project_id'])
        ->chunkById(200, static function ($sprints) use ($yesterday): void {
            foreach ($sprints as $s) {
                dispatch(new BuildSprintDailyReportsJob($s->project_id, $s->id, $yesterday));
            }
        });
})->dailyAt('01:25');

Schedule::call(static function (): void {
    Project::query()->select('id')->chunkById(200, static function ($projects): void {
        foreach ($projects as $p) {
            dispatch(new RefreshOpenIssueAgesJob($p->id));
        }
    });
})->dailyAt('01:40');

if (config('health.queue_check_enabled') && config('queue.default') !== 'sync') {
    Schedule::command(DispatchQueueCheckJobsCommand::class)
        ->everyMinute()
        ->withoutOverlapping(5);
}
