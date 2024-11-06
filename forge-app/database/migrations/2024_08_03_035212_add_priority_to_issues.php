<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to add a priority column to the issues table.
 *
 * This migration adds a new column named 'priority' to the 'issues' table
 * in the database. The 'priority' column is intended to store the priority
 * level of each issue.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->bigInteger('priority_id')->unsigned();
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->foreign('priority_id')->references('id')->on('issue_priorities');
        });

        //set constraints, unique
        Schema::table('issues', function (Blueprint $table) {
            $table->unique(['priority_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropForeign(['priority_id']);
            $table->dropColumn('priority_id');
        });
    }
};
