<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // Enforce "one running timer per user"
        if (in_array($driver, ['sqlite', 'pgsql'], true)) {
            // Partial unique index: (user_id) WHERE ended_at IS NULL
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS time_entries_unique_running_user ON time_entries (user_id) WHERE ended_at IS NULL');
        } else {
            // MySQL / MariaDB: functional unique index on expression
            if (! $this->indexExistsMySql('time_entries', 'time_entries_unique_running_user')) {
                DB::statement('CREATE UNIQUE INDEX `time_entries_unique_running_user` ON `time_entries` ((CASE WHEN `ended_at` IS NULL THEN `user_id` ELSE NULL END))');
            }
        }

        // Secondary indexes (portable)
        $this->ensureCompositeIndex('time_entries', 'time_entries_user_ended_at', ['user_id', 'ended_at'], $driver);
        $this->ensureCompositeIndex('time_entries', 'time_entries_issue_started_at', ['issue_id', 'started_at'], $driver);
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // Drop unique constraint for "one running timer per user"
        if (in_array($driver, ['sqlite', 'pgsql'], true)) {
            DB::statement('DROP INDEX IF EXISTS time_entries_unique_running_user');
        } else {
            if ($this->indexExistsMySql('time_entries', 'time_entries_unique_running_user')) {
                DB::statement('DROP INDEX `time_entries_unique_running_user` ON `time_entries`');
            }
        }

        // Drop secondary indexes
        $this->dropIndexPortable('time_entries', 'time_entries_user_ended_at', $driver);
        $this->dropIndexPortable('time_entries', 'time_entries_issue_started_at', $driver);
    }

    // ----- helpers -----------------------------------------------------------

    /** Ensure composite index exists (portable). */
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

        $cols = implode(', ', array_map(static fn ($c) => $driver === 'pgsql' ? "\"$c\"" : $c, $columns));
        DB::statement("CREATE INDEX IF NOT EXISTS {$index} ON {$table} ({$cols})");
    }

    private function dropIndexPortable(string $table, string $index, string $driver): void
    {
        if ($driver === 'mysql') {
            if ($this->indexExistsMySql($table, $index)) {
                Schema::table($table, static function (Blueprint $t) use ($index): void {
                    $t->dropIndex($index);
                });
            }
            return;
        }

        DB::statement("DROP INDEX IF EXISTS {$index}");
    }

    private function indexExistsMySql(string $table, string $index): bool
    {
        $row = DB::selectOne("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
        return $row !== null;
    }
};
