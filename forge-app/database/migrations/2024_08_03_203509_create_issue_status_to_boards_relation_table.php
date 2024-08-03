<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Defines the relationship between issue statuses and boards, statuses are represented as the stages of Kanban boards.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('issue_status_to_boards_relation', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('stage_id')->unsigned();
            $table->bigInteger('board_id')->unsigned();
            $table->integer('sort')->default(1);
            $table->boolean('is_default')->default(false);
            $table->string('color')->nullable()->default(null); // color of the stage for the board only, it will use the status color if not set
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('issue_status_to_boards_relation', function (Blueprint $table) {
            $table->foreign('stage_id')->references('id')->on('issue_statuses');
            $table->foreign('board_id')->references('id')->on('boards');
        });

        // Add a unique constraint to prevent duplicate relations
        Schema::table('issue_status_to_boards_relation', function (Blueprint $table) {
            $table->unique(['stage_id', 'board_id'], 'unique_stage_board_relations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_status_to_boards_relation');
    }
};
