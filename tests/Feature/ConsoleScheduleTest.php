<?php

namespace Tests\Feature;

use App\Console\Commands\ReverbHealthCheck;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;
use Tests\TestCase;

class ConsoleScheduleTest extends TestCase
{
    public function test_schedule_health_heartbeat_runs_every_minute(): void
    {
        $event = collect(app(Schedule::class)->events())->first(
            fn (Event $event) => str_contains((string) $event->command, ScheduleCheckHeartbeatCommand::class)
                || str_contains((string) $event->command, 'health:schedule-check-heartbeat')
        );

        $this->assertNotNull($event);
        $this->assertSame('* * * * *', $event->expression);
        $this->assertTrue($event->withoutOverlapping);
    }

    public function test_queue_heartbeat_schedule_is_disabled_by_default(): void
    {
        $hasQueueHeartbeat = collect(app(Schedule::class)->events())->contains(
            fn (Event|CallbackEvent $event) => str_contains((string) $event->command, DispatchQueueCheckJobsCommand::class)
                || str_contains((string) $event->command, 'health:queue-check-heartbeat')
        );

        $this->assertFalse($hasQueueHeartbeat);
    }

    public function test_reverb_health_schedule_is_disabled_by_default(): void
    {
        $hasReverbHealth = collect(app(Schedule::class)->events())->contains(
            fn (Event|CallbackEvent $event) => str_contains((string) $event->command, ReverbHealthCheck::class)
                || str_contains((string) $event->command, 'reverb:health')
        );

        $this->assertFalse($hasReverbHealth);
    }

    public function test_remote_dependent_schedules_use_overlap_protection(): void
    {
        $repoSync = collect(app(Schedule::class)->events())->first(
            fn (Event|CallbackEvent $event) => str_contains((string) $event->command, 'repo:sync --all')
        );

        $healthCheck = collect(app(Schedule::class)->events())->first(
            fn (Event|CallbackEvent $event) => str_contains((string) $event->command, RunHealthChecksCommand::class)
                || str_contains((string) $event->command, 'health:check')
        );

        $this->assertNotNull($repoSync);
        $this->assertTrue($repoSync->withoutOverlapping);
        $this->assertNotNull($healthCheck);
        $this->assertTrue($healthCheck->withoutOverlapping);
    }
}
