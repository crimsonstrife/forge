<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('issues', static function (Blueprint $table) {
            if (! Schema::hasColumn('issues', 'planning_order')) {
                $table->unsignedInteger('planning_order')->nullable();
                $table->index(
                    ['project_id', 'sprint_id', 'planning_order'],
                    'issues_project_sprint_planning_order_idx'
                );
            }
        });

        Schema::table('sprints', static function (Blueprint $table) {
            if (! Schema::hasColumn('sprints', 'capacity')) {
                $table->unsignedInteger('capacity')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('issues', static function (Blueprint $table) {
            if (Schema::hasColumn('issues', 'planning_order')) {
                $table->dropIndex('issues_project_sprint_planning_order_idx');
                $table->dropColumn('planning_order');
            }
        });

        Schema::table('sprints', static function (Blueprint $table) {
            if (Schema::hasColumn('sprints', 'capacity')) {
                $table->dropColumn('capacity');
            }
        });
    }
};
