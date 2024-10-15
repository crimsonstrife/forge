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
        Schema::table('issue_statuses', function (Blueprint $table) {
            $table->bigInteger('project_id')->unsigned()->nullable();
        });

        Schema::table('issue_statuses', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
        });

        // Ensure that is_default is unique per project_id.  This will allow us to have a default status per project, with NULL being a global default.
        Schema::table('issue_statuses', function (Blueprint $table) {
            $table->unique(['project_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issue_statuses', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};
