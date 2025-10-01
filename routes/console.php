<?php

use App\Console\Commands\CheckForAppUpdate;
use App\Console\Commands\RecalcIssueRollups;
use App\Console\Commands\ReverbHealthCheck;
use App\Console\Commands\SyncRepositoryIssues;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Spatie\Health\Commands\RunHealthChecksCommand;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(CheckForAppUpdate::class)
    ->dailyAt('09:00');

Schedule::command(SyncRepositoryIssues::class)
    ->everyFifteenMinutes();

Schedule::command(RecalcIssueRollups::class)
    ->everyFiveMinutes();

Schedule::command(ReverbHealthCheck::class)
    ->everyFiveMinutes();

Schedule::command(RunHealthChecksCommand::class)
    ->everyMinute();
