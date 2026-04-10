<?php

namespace Tests\Feature;

use App\Session\ResilientStore;
use SessionHandlerInterface;
use Tests\TestCase;

class RedisAndSessionResilienceTest extends TestCase
{
    public function test_tls_redis_defaults_to_phpredis_client(): void
    {
        $this->assertSame('phpredis', config('database.redis.client'));
    }

    public function test_corrupt_session_payloads_are_dropped_instead_of_crashing(): void
    {
        $initialSessionId = str_repeat('a', 40);
        $handler = new CorruptPayloadSessionHandler($this->corruptTypedPropertySessionPayload());
        $store = new ResilientStore('forge_session', $handler, $initialSessionId);

        $store->start();

        $this->assertNull($store->get('test'));
        $this->assertContains($initialSessionId, $handler->destroyedSessionIds);
        $this->assertNotSame($initialSessionId, $store->getId());
        $this->assertTrue($store->has('_token'));
    }

    private function corruptTypedPropertySessionPayload(): string
    {
        $serializedObject = sprintf(
            'O:%d:"%s":1:{s:5:"value";N;}',
            strlen(CorruptTypedStringHolder::class),
            CorruptTypedStringHolder::class
        );

        return sprintf('a:1:{s:4:"test";%s}', $serializedObject);
    }
}

class CorruptTypedStringHolder
{
    public string $value;
}

class CorruptPayloadSessionHandler implements SessionHandlerInterface
{
    /**
     * @param  list<string>  $destroyedSessionIds
     */
    public function __construct(
        private readonly string $payload,
        public array $destroyedSessionIds = [],
    ) {}

    public function open($savePath, $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string
    {
        return $this->payload;
    }

    public function write($id, $data): bool
    {
        return true;
    }

    public function destroy($id): bool
    {
        $this->destroyedSessionIds[] = $id;

        return true;
    }

    public function gc($max_lifetime): int|false
    {
        return 0;
    }
}
