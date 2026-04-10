<?php

namespace Tests\Feature;

use App\Providers\HealthServiceProvider;
use Spatie\Health\Checks\Checks\QueueCheck;
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

    public function test_health_service_provider_skips_queue_check_when_using_sync_driver(): void
    {
        config()->set('queue.default', 'sync');

        $this->bootHealthServiceProvider();

        $this->assertFalse(
            Health::registeredChecks()->contains(fn ($check) => $check instanceof QueueCheck)
        );
    }

    public function test_health_service_provider_registers_serializable_queue_check_for_async_drivers(): void
    {
        config()->set('queue.default', 'database');

        $this->bootHealthServiceProvider();

        $queueCheck = Health::registeredChecks()->sole(
            fn ($check) => $check instanceof QueueCheck
        );

        $restored = unserialize(serialize(new HealthQueueJob($queueCheck)));

        $this->assertInstanceOf(HealthQueueJob::class, $restored);
    }

    protected function bootHealthServiceProvider(): void
    {
        (new HealthServiceProvider($this->app))->boot();
    }
}
