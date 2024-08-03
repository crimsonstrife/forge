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
        Schema::table('stories', function (Blueprint $table) {
            $table->bigInteger('related_issue_id')->unsigned()->nullable();
            $table->string('issue_relation_type');
            $table->integer('issue_sort')->default(1);
            $table->bigInteger('related_story_id')->unsigned()->nullable();
            $table->string('story_relation_type');
            $table->integer('story_sort')->default(1);
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->bigInteger('related_story_id')->unsigned()->nullable();
            $table->string('story_relation_type');
            $table->integer('story_sort')->default(1);
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->foreign('related_issue_id')->references('id')->on('issues');
            $table->foreign('related_story_id')->references('id')->on('stories');
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->foreign('related_story_id')->references('id')->on('stories');
        });

        // Add a unique constraint to prevent duplicate relations
        Schema::table('stories', function (Blueprint $table) {
            $table->unique(['related_issue_id', 'issue_relation_type'], 'unique_issue_relations');
            $table->unique(['related_story_id', 'story_relation_type'], 'unique_story_relations');
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->unique(['related_story_id', 'story_relation_type'], 'unique_story_relations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->dropForeign(['related_issue_id']);
            $table->dropForeign(['related_story_id']);
            $table->dropColumn('related_issue_id');
            $table->dropColumn('issue_relation_type');
            $table->dropColumn('issue_sort');
            $table->dropColumn('related_story_id');
            $table->dropColumn('story_relation_type');
            $table->dropColumn('story_sort');
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->dropForeign(['related_story_id']);
            $table->dropColumn('related_story_id');
            $table->dropColumn('story_relation_type');
            $table->dropColumn('story_sort');
        });
    }
};
