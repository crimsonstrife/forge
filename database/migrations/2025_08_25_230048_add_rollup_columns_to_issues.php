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
        Schema::table('issues', static function (Blueprint $table) {
            $table->unsignedInteger('children_count')->default(0)->after('parent_id');
            $table->unsignedInteger('children_done_count')->default(0)->after('children_count');
            $table->unsignedInteger('children_points_total')->default(0)->after('children_done_count');
            $table->unsignedInteger('children_points_done')->default(0)->after('children_points_total');
            $table->unsignedSmallInteger('progress_percent')->default(0)->after('children_points_done'); // 0..100
            $table->index(['parent_id', 'issue_status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropIndex(['parent_id', 'issue_status_id']);
            $table->dropColumn([
                'children_count','children_done_count',
                'children_points_total','children_points_done','progress_percent',
            ]);
        });
    }
};
