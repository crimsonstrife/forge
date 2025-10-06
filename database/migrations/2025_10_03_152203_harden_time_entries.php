<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // Add generated column if missing (used to enforce "one running timer per user")
        if (! Schema::hasColumn('time_entries', 'running_user_id')) {
            Schema::table('time_entries', static function (Blueprint $t): void {
                // STORED keeps this indexable across MySQL/MariaDB variants.
                $t->char('running_user_id', 36)
                    ->nullable()
                    ->storedAs('IF(ended_at IS NULL, user_id, NULL)');
            });
        }

        // Create/ensure indexes (only if they don't already exist)
        $this->ensureIndex('time_entries', 'time_entries_unique_running_user', static function () {
            Schema::table('time_entries', static function (Blueprint $t): void {
                $t->unique('running_user_id', 'time_entries_unique_running_user');
            });
        }, ['time_entries_running_user_id_unique']);

        $this->ensureIndex('time_entries', 'time_entries_user_ended_at', static function () {
            Schema::table('time_entries', static function (Blueprint $t): void {
                $t->index(['user_id', 'ended_at'], 'time_entries_user_ended_at');
            });
        });

        $this->ensureIndex('time_entries', 'time_entries_issue_started_at', static function () {
            Schema::table('time_entries', static function (Blueprint $t): void {
                $t->index(['issue_id', 'started_at'], 'time_entries_issue_started_at');
            });
        });
    }

    public function down(): void
    {
        // Drop indexes if present
        $this->dropIndexIfExists('time_entries', 'time_entries_unique_running_user');
        $this->dropIndexIfExists('time_entries', 'time_entries_running_user_id_unique'); // fallback name
        $this->dropIndexIfExists('time_entries', 'time_entries_user_ended_at');
        $this->dropIndexIfExists('time_entries', 'time_entries_issue_started_at');

        // Drop column if present
        if (Schema::hasColumn('time_entries', 'running_user_id')) {
            Schema::table('time_entries', static function (Blueprint $t): void {
                $t->dropColumn('running_user_id');
            });
        }
    }

    // --- helpers -------------------------------------------------------------

    /** Ensure an index exists; if not, run the creator callback. */
    private function ensureIndex(string $table, string $indexName, callable $creator, array $alsoCheck = []): void
    {
        if ($this->indexExists($table, $indexName)) {
            return;
        }

        // Check any alternate names that may already exist
        foreach ($alsoCheck as $alt) {
            if ($this->indexExists($table, $alt)) {
                return;
            }
        }

        $creator();
    }

    /** Drop an index if it exists (safe across environments). */
    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            Schema::table($table, static function (Blueprint $t) use ($indexName): void {
                $t->dropIndex($indexName);
            });
        }
    }

    /** Check information_schema for an index by name. */
    private function indexExists(string $table, string $indexName): bool
    {
        $db = DB::getDatabaseName();

        $row = DB::selectOne(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$db, $table, $indexName]
        );

        return $row !== null;
    }
};
