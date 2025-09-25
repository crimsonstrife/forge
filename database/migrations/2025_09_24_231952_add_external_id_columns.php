<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        $tables = [
            'projects', 'issues', 'comments', 'issue_links',
            'time_entries', 'media', 'tags',
            'issue_statuses', 'issue_types', 'issue_priorities',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t): void {
                if (! Schema::hasColumn($t->getTable(), 'external_id')) {
                    $t->uuid('external_id')->nullable()->unique()->index();
                }
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'projects', 'issues', 'comments', 'issue_links',
            'time_entries', 'media', 'tags',
            'issue_statuses', 'issue_types', 'issue_priorities',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) use ($table): void {
                if (Schema::hasColumn($table, 'external_id')) {
                    $t->dropUnique([$table.'_external_id_unique']);
                    $t->dropIndex([$table.'_external_id_index']);
                    $t->dropColumn('external_id');
                }
            });
        }
    }
};
