<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('issues', static function (Blueprint $table) {
            $table->uuid('sprint_id')->nullable()->after('project_id');
            $table->foreign('sprint_id')->references('id')->on('sprints')->nullOnDelete();
            $table->index(['project_id', 'sprint_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', static function (Blueprint $table) {
            $table->dropForeign(['sprint_id']);
            $table->dropIndex(['project_id', 'sprint_id']);
            $table->dropColumn('sprint_id');
        });
    }
};
