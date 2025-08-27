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
        if (Schema::hasTable('project_team') && Schema::hasColumn('project_team', 'id')) {
            $this->dropPrimaryIfExists('project_team');
            $this->dropColumnRaw('project_team', 'id');
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
