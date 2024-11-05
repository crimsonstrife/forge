<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


/**
 * CreateIssueRelationsTable migration class.
 *
 * This migration creates the issue_relations table in the database.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('issue_relations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('issue_id')->unsigned();
            $table->bigInteger('related_issue_id')->unsigned();
            $table->bigInteger('issue_relation_type')->unsigned();
            $table->integer('issue_sort')->default(1);
            $table->timestamps();
        });

        Schema::table('issue_relations', function (Blueprint $table) {
            $table->foreign('issue_id')->references('id')->on('issues');
            $table->foreign('related_issue_id')->references('id')->on('issues');
        });

        // Add a unique constraint to prevent duplicate relations
        Schema::table('issue_relations', function (Blueprint $table) {
            $table->unique(['issue_id', 'related_issue_id', 'issue_relation_type'], 'unique_issue_relations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_relations');
    }
};
