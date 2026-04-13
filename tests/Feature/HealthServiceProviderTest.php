<?php

namespace Tests\Feature;

use App\Providers\HealthServiceProvider;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Facades\Health;
use Spatie\Health\Jobs\HealthQueueJob;
use Tests\TestCase;

class HealthServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Health::clearChecks();
    }

    public function test_health_service_provider_skips_queue_check_when_not_explicitly_enabled(): void
    {
        config()->set('queue.default', 'database');
        config()->set('health.queue_check_enabled', false);

        $this->bootHealthServiceProvider();

        $this->assertFalse(
            Health::registeredChecks()->contains(fn ($check) => $check instanceof QueueCheck)
        );
    }

    public function test_health_service_provider_registers_serializable_queue_check_when_enabled(): void
    {
        config()->set('queue.default', 'database');
        config()->set('health.queue_check_enabled', true);

        $this->bootHealthServiceProvider();

        $queueCheck = Health::registeredChecks()->sole(
            fn ($check) => $check instanceof QueueCheck
        );

        $this->assertSame('file', $queueCheck->getCacheStoreName());

        $restored = unserialize(serialize(new HealthQueueJob($queueCheck)));

        $this->assertInstanceOf(HealthQueueJob::class, $restored);
    }

    public function test_health_service_provider_uses_file_cache_for_schedule_heartbeats(): void
    {
        $this->bootHealthServiceProvider();

        $scheduleCheck = Health::registeredChecks()->sole(
            fn ($check) => $check instanceof ScheduleCheck
        );

        $this->assertSame('file', $scheduleCheck->getCacheStoreName());
    }

    protected function bootHealthServiceProvider(): void
    {
        (new HealthServiceProvider($this->app))->boot();
    }
}
