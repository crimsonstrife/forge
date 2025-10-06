<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Harden time_entries:
 * - Enforce "one running timer per user".
 * - Add helpful secondary indexes.
 *
 * MySQL/MariaDB: generated column + unique index.
 * SQLite/Postgres: partial unique index on (user_id) WHERE ended_at IS NULL.
 */
return new class () extends Migration {
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // --- Enforce one running timer per user --------------------------------
        if ($this->supportsPartialUnique($driver)) {
            // SQLite / Postgres: partial unique index
            $this->createPartialUniqueIndex(
                table: 'time_entries',
                index: 'time_entries_unique_running_user',
                columns: ['user_id'],
                where: 'ended_at IS NULL',
                driver: $driver,
            );
        } else {
            // MySQL/MariaDB: generated column + unique index
            if (! Schema::hasColumn('time_entries', 'running_user_id')) {
                Schema::table('time_entries', static function (Blueprint $t): void {
                    // Match your UUID length; char(36) is typical
                    $t->char('running_user_id', 36)
                        ->nullable()
                        ->storedAs("(case when `ended_at` is null then `user_id` else null end)");
                });
            }
            $this->ensureIndexMySql(
                table: 'time_entries',
                index: 'time_entries_unique_running_user',
                creator: static function (): void {
                    Schema::table('time_entries', static function (Blueprint $t): void {
                        $t->unique('running_user_id', 'time_entries_unique_running_user');
                    });
                },
                alsoCheck: ['time_entries_running_user_id_unique']
            );
        }

        // --- Secondary indexes --------------------------------------------------
        $this->ensureCompositeIndex(
            table: 'time_entries',
            index: 'time_entries_user_ended_at',
            columns: ['user_id', 'ended_at'],
            driver: $driver
        );

        $this->ensureCompositeIndex(
            table: 'time_entries',
            index: 'time_entries_issue_started_at',
            columns: ['issue_id', 'started_at'],
            driver: $driver
        );
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // Drop unique/rule for "one running timer per user"
        if ($this->supportsPartialUnique($driver)) {
            // SQLite / Postgres
            $this->dropIndexRaw('time_entries_unique_running_user', $driver);
        } else {
            // MySQL/MariaDB
            $this->dropIndexMySql('time_entries', 'time_entries_unique_running_user');
            $this->dropIndexMySql('time_entries', 'time_entries_running_user_id_unique'); // fallback name
            if (Schema::hasColumn('time_entries', 'running_user_id')) {
                Schema::table('time_entries', static function (Blueprint $t): void {
                    $t->dropColumn('running_user_id');
                });
            }
        }

        // Drop secondary indexes
        $this->dropIndexPortable('time_entries', 'time_entries_user_ended_at', $driver);
        $this->dropIndexPortable('time_entries', 'time_entries_issue_started_at', $driver);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function supportsPartialUnique(string $driver): bool
    {
        return in_array($driver, ['sqlite', 'pgsql'], true);
    }

    /**
     * Create partial unique index (SQLite/Postgres).
     *
     * @param array<int,string> $columns
     */
    private function createPartialUniqueIndex(string $table, string $index, array $columns, string $where, string $driver): void
    {
        $cols = implode(', ', array_map(static fn ($c) => $driver === 'pgsql' ? "\"$c\"" : $c, $columns));

        if ($driver === 'sqlite') {
            DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS {$index} ON {$table} ({$cols}) WHERE {$where}");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS {$index} ON {$table} ({$cols}) WHERE {$where}");
            return;
        }

        // Should never hit for MySQL because supportsPartialUnique()==false there.
    }

    /**
     * Ensure composite index exists (portable).
     *
     * @param array<int,string> $columns
     */
    private function ensureCompositeIndex(string $table, string $index, array $columns, string $driver): void
    {
        if ($driver === 'mysql') {
            if (! $this->indexExistsMySql($table, $index)) {
                Schema::table($table, static function (Blueprint $t) use ($columns, $index): void {
                    $t->index($columns, $index);
                });
            }
            return;
        }

        // SQLite / Postgres: IF NOT EXISTS available
        $cols = implode(', ', array_map(static fn ($c) => $driver === 'pgsql' ? "\"$c\"" : $c, $columns));
        DB::statement("CREATE INDEX IF NOT EXISTS {$index} ON {$table} ({$cols})");
    }

    private function ensureIndexMySql(string $table, string $index, callable $creator, array $alsoCheck = []): void
    {
        if ($this->indexExistsMySql($table, $index)) {
            return;
        }
        foreach ($alsoCheck as $alt) {
            if ($this->indexExistsMySql($table, $alt)) {
                return;
            }
        }
        $creator(); // will add the index via Schema::table()
    }

    private function indexExistsMySql(string $table, string $index): bool
    {
        $row = DB::selectOne("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
        return $row !== null;
    }

    private function dropIndexMySql(string $table, string $index): void
    {
        if ($this->indexExistsMySql($table, $index)) {
            Schema::table($table, static function (Blueprint $t) use ($index): void {
                $t->dropIndex($index);
            });
        }
    }

    private function dropIndexRaw(string $index, string $driver): void
    {
        if ($driver === 'sqlite') {
            DB::statement("DROP INDEX IF EXISTS {$index}");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("DROP INDEX IF EXISTS {$index}");
            return;
        }
    }

    private function dropIndexPortable(string $table, string $index, string $driver): void
    {
        if ($driver === 'mysql') {
            $this->dropIndexMySql($table, $index);
            return;
        }
        $this->dropIndexRaw($index, $driver);
    }
};
