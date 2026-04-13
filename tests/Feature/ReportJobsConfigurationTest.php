<?php

namespace Tests\Feature;

use App\Jobs\BuildProjectDailyReportsJob;
use App\Jobs\BuildSprintDailyReportsJob;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReportJobsConfigurationTest extends TestCase
{
    public function test_daily_report_jobs_configure_retries_and_backoff(): void
    {
        $projectJob = new BuildProjectDailyReportsJob('project-1', Carbon::parse('2026-04-12'));
        $sprintJob = new BuildSprintDailyReportsJob('project-1', 'sprint-1', Carbon::parse('2026-04-12'));

        $this->assertSame(5, $projectJob->tries);
        $this->assertSame([5, 15, 30, 60], $projectJob->backoff);
        $this->assertSame(5, $sprintJob->tries);
        $this->assertSame([5, 15, 30, 60], $sprintJob->backoff);
    }
}
