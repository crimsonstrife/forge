<?php

use App\Console\Commands\CheckForAppUpdate;
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
use Spatie\Health\Commands\RunHealthChecksCommand;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(CheckForAppUpdate::class)
    ->dailyAt('09:00');

Schedule::command(SendIssueNotificationDigests::class)
    ->dailyAt('08:00');

Schedule::command(SyncRepositoryIssues::class, ['--all'])
    ->everyFifteenMinutes();

Schedule::command(RecalcIssueRollups::class)
    ->everyFiveMinutes();

Schedule::command(ReverbHealthCheck::class)
    ->everyFiveMinutes();

Schedule::command(RunHealthChecksCommand::class)
    ->everyMinute();

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
