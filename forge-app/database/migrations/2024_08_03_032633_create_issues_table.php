<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * This migration class is responsible for creating the 'issues' table in the database.
 *
 * The table will be created when the migration is run, and it will be dropped when the migration is rolled back.
 *
 * The class extends the base Migration class provided by the Laravel framework.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content');
            $table->bigInteger('owner_id')->unsigned()->nullable();
            $table->bigInteger('responsible_id')->unsigned()->nullable();
            $table->bigInteger('project_id')->unsigned();
            $table->bigInteger('issue_type_id')->unsigned();
            $table->bigInteger('issue_status_id')->unsigned();
            $table->string('created_by')->nullable(); // For tracking the user who submitted the issue (e.g., Discord user)
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('responsible_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('issue_type_id')->references('id')->on('issue_types');
            $table->foreign('issue_status_id')->references('id')->on('issue_statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
