<?php

namespace Tests\Feature;

use Illuminate\Cache\Events\CacheFailedOver;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Tests\TestCase;

class CacheFailoverStoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::extend('failing-test', function ($app, array $config) {
            return $app['cache']->repository(new ExplodingStore, $config);
        });
    }

    public function test_failover_store_uses_fallback_for_basic_cache_operations_and_locks(): void
    {
        $this->configureFailoverStore();

        $cache = Cache::store('failover');

        $this->assertTrue($cache->put('reports:daily', 'ok', 60));
        $this->assertSame('ok', $cache->get('reports:daily'));

        $this->assertTrue($cache->forever('reports:weekly', 'fresh'));
        $this->assertSame('fresh', $cache->get('reports:weekly'));

        $this->assertTrue($cache->forget('reports:weekly'));
        $this->assertNull($cache->get('reports:weekly'));

        $result = $cache->lock('support:ticket-key', 5)->block(1, static fn (): string => 'locked');

        $this->assertSame('locked', $result);
    }

    public function test_failover_warning_is_throttled_across_recreated_failover_repositories(): void
    {
        $this->configureFailoverStore();
        Log::spy();

        event(new CacheFailedOver('failing-test-primary', new RuntimeException('php_network_getaddresses: getaddrinfo for redis.example.test failed: Temporary failure in name resolution')));
        event(new CacheFailedOver('failing-test-primary', new RuntimeException('php_network_getaddresses: getaddrinfo for redis.example.test failed: Temporary failure in name resolution')));

        Log::shouldHaveReceived('warning')
            ->once()
            ->withArgs(static function (string $message, array $context): bool {
                return $message === 'Primary cache store failed over to the configured fallback store.'
                    && $context['store'] === 'failing-test-primary'
                    && $context['fallback_store'] === 'array-fallback'
                    && $context['exception'] === RuntimeException::class;
            });
    }

    private function configureFailoverStore(): void
    {
        config()->set('cache.stores.failing-test-primary', [
            'driver' => 'failing-test',
        ]);

        config()->set('cache.stores.array-fallback', [
            'driver' => 'array',
            'serialize' => false,
        ]);

        config()->set('cache.stores.failover', [
            'driver' => 'failover',
            'primary_store' => 'failing-test-primary',
            'fallback_store' => 'array-fallback',
            'log_throttle_minutes' => 30,
            'stores' => ['failing-test-primary', 'array-fallback'],
        ]);

        foreach (['failing-test-primary', 'array-fallback', 'failover'] as $driver) {
            app('cache')->forgetDriver($driver);
        }
    }
}

final class ExplodingStore implements LockProvider, Store
{
    public function get($key)
    {
        throw $this->failure();
    }

    public function many(array $keys)
    {
        throw $this->failure();
    }

    public function put($key, $value, $seconds)
    {
        throw $this->failure();
    }

    public function putMany(array $values, $seconds)
    {
        throw $this->failure();
    }

    public function increment($key, $value = 1)
    {
        throw $this->failure();
    }

    public function decrement($key, $value = 1)
    {
        throw $this->failure();
    }

    public function forever($key, $value)
    {
        throw $this->failure();
    }

    public function forget($key)
    {
        throw $this->failure();
    }

    public function flush()
    {
        throw $this->failure();
    }

    public function getPrefix()
    {
        return 'failing-test:';
    }

    public function add($key, $value, $seconds)
    {
        throw $this->failure();
    }

    public function lock($name, $seconds = 0, $owner = null): Lock
    {
        throw $this->failure();
    }

    public function restoreLock($name, $owner): Lock
    {
        throw $this->failure();
    }

    private function failure(): RuntimeException
    {
        return new RuntimeException('php_network_getaddresses: getaddrinfo for redis.example.test failed: Temporary failure in name resolution');
    }
}
