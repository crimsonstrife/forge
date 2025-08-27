<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ---- project_issue_types
        if (Schema::hasTable('project_issue_types') && Schema::hasColumn('project_issue_types', 'id')) {
            $this->dropPrimaryIfExists('project_issue_types');
            $this->dropColumnRaw('project_issue_types', 'id');
        }

        // ---- project_issue_statuses
        if (Schema::hasTable('project_issue_statuses') && Schema::hasColumn('project_issue_statuses', 'id')) {
            $this->dropPrimaryIfExists('project_issue_statuses');
            $this->dropColumnRaw('project_issue_statuses', 'id');
        }

        // ---- project_issue_priorities
        if (Schema::hasTable('project_issue_priorities') && Schema::hasColumn('project_issue_priorities', 'id')) {
            $this->dropPrimaryIfExists('project_issue_priorities');
            $this->dropColumnRaw('project_issue_priorities', 'id');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }

    private function dropPrimaryIfExists(string $table): void
    {
        try {
            DB::statement("ALTER TABLE `{$table}` DROP PRIMARY KEY");
        } catch (\Throwable $e) {
            // ignore if no primary key or already dropped
        }
    }

    private function dropColumnRaw(string $table, string $column): void
    {
        try {
            DB::statement("ALTER TABLE `{$table}` DROP COLUMN `{$column}`");
        } catch (\Throwable $e) {
            // ignore if the column was already dropped
        }
    }
};
