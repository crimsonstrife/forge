<?php

namespace Tests\Feature;

use App\Console\Commands\DeleteExpiredBans;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PDOException;
use Tests\TestCase;

final class DeleteExpiredBansCommandTest extends TestCase
{
    public function test_it_logs_transient_database_name_resolution_failures_without_failing(): void
    {
        Cache::store('file')->forget(DeleteExpiredBans::TRANSIENT_FAILURE_LOG_CACHE_KEY);
        Log::spy();

        ExpiredBanCleanupTestModel::$deleteAttempts = 0;
        ExpiredBanCleanupTestModel::$exception = self::queryException(
            'SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo for dbaas-db-1505749-do-user-6423046-0.h.db.ondigitalocean.com failed: Temporary failure in name resolution',
            2002,
        );

        config(['ban.model' => ExpiredBanCleanupTestModel::class]);

        $this->artisan('bans:delete-expired')
            ->expectsOutput('Skipped expired ban cleanup: temporary database DNS resolution failure.')
            ->assertExitCode(Command::SUCCESS);

        $this->artisan('bans:delete-expired')
            ->expectsOutput('Skipped expired ban cleanup: temporary database DNS resolution failure.')
            ->assertExitCode(Command::SUCCESS);

        self::assertSame(2, ExpiredBanCleanupTestModel::$deleteAttempts);

        Log::shouldHaveReceived('warning')
            ->once()
            ->withArgs(static function (string $message, array $context): bool {
                return $message === 'Expired ban cleanup skipped because the database host could not be resolved.'
                    && $context['connection'] === 'mysql'
                    && $context['exception'] === QueryException::class
                    && $context['code'] === 2002
                    && $context['previous_code'] === 2002
                    && $context['suppressed_for_minutes'] === 30;
            });
    }

    public function test_it_rethrows_non_transient_database_failures(): void
    {
        ExpiredBanCleanupTestModel::$deleteAttempts = 0;
        ExpiredBanCleanupTestModel::$exception = self::queryException(
            'SQLSTATE[42S22]: Column not found: 1054 Unknown column "missing_column" in "where clause"',
            1054,
        );

        config(['ban.model' => ExpiredBanCleanupTestModel::class]);

        $this->expectException(QueryException::class);

        app(DeleteExpiredBans::class)->handle();
    }

    private static function queryException(string $message, int $code): QueryException
    {
        return new QueryException(
            'mysql',
            'update `bans` set `deleted_at` = ? where `expired_at` is not null',
            ['2026-04-04 17:37:53'],
            new PDOException($message, $code),
            [
                'driver' => 'mysql',
                'host' => 'dbaas-db-1505749-do-user-6423046-0.h.db.ondigitalocean.com',
                'port' => 25060,
                'database' => 'forge_db',
            ],
        );
    }
}

final class ExpiredBanCleanupTestModel
{
    public static int $deleteAttempts = 0;

    public static PDOException $exception;

    public static function expired(): object
    {
        return new class
        {
            public function delete(): void
            {
                ExpiredBanCleanupTestModel::$deleteAttempts++;

                throw ExpiredBanCleanupTestModel::$exception;
            }
        };
    }
}
