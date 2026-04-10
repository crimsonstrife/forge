<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
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
    }
}
