<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mchev\Banhammer\Banhammer;
use PDOException;
use Throwable;

final class DeleteExpiredBans extends Command
{
    public const TRANSIENT_FAILURE_LOG_CACHE_KEY = 'banhammer:expired-ban-cleanup:transient-db-dns-warning';

    private const TRANSIENT_FAILURE_LOG_THROTTLE_MINUTES = 30;

    protected $signature = 'bans:delete-expired';

    protected $description = 'Soft-delete expired ban records';

    public function handle(): int
    {
        try {
            Banhammer::unbanExpired();
        } catch (PDOException $exception) {
            if (! $this->isTransientDatabaseNameResolutionFailure($exception)) {
                throw $exception;
            }

            if ($this->shouldLogTransientFailure()) {
                Log::warning('Expired ban cleanup skipped because the database host could not be resolved.', [
                    'connection' => $exception instanceof QueryException
                        ? $exception->getConnectionName()
                        : config('database.default'),
                    'database' => config('database.connections.'.config('database.default').'.database'),
                    'host' => config('database.connections.'.config('database.default').'.host'),
                    'port' => config('database.connections.'.config('database.default').'.port'),
                    'exception' => $exception::class,
                    'code' => $exception->getCode(),
                    'previous_code' => $exception->getPrevious()?->getCode(),
                    'error' => $exception->getPrevious()?->getMessage() ?: $exception->getMessage(),
                    'suppressed_for_minutes' => self::TRANSIENT_FAILURE_LOG_THROTTLE_MINUTES,
                ]);
            }

            $this->warn('Skipped expired ban cleanup: temporary database DNS resolution failure.');

            return self::SUCCESS;
        }

        $this->info('Expired bans have been deleted.');

        return self::SUCCESS;
    }

    private function isTransientDatabaseNameResolutionFailure(PDOException $exception): bool
    {
        $message = Str::lower(implode(' ', array_filter([
            $exception->getMessage(),
            $exception->getPrevious()?->getMessage(),
        ])));

        $isMySqlConnectionError = in_array('2002', [
            (string) $exception->getCode(),
            (string) $exception->getPrevious()?->getCode(),
        ], true) || Str::contains($message, 'sqlstate[hy000] [2002]');

        if (! $isMySqlConnectionError) {
            return false;
        }

        return Str::contains($message, [
            'php_network_getaddresses',
            'getaddrinfo',
            'temporary failure in name resolution',
            'could not translate host name',
        ]);
    }

    private function shouldLogTransientFailure(): bool
    {
        try {
            return Cache::store('file')->add(
                self::TRANSIENT_FAILURE_LOG_CACHE_KEY,
                true,
                now()->addMinutes(self::TRANSIENT_FAILURE_LOG_THROTTLE_MINUTES),
            );
        } catch (Throwable) {
            return true;
        }
    }
}
