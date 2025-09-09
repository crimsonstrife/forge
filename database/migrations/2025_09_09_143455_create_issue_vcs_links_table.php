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
        Schema::create('issue_vcs_links', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('issue_id');
            $table->uuid('repository_id');
            $table->enum('type', ['branch','pull_request']);
            $table->string('external_id')->nullable();   // GH node_id / PR id if desired
            $table->string('name')->nullable();          // branch name OR PR title
            $table->unsignedInteger('number')->nullable(); // PR number if applicable
            $table->string('url')->nullable();
            $table->string('state')->nullable();         // open|closed|merged, etc.
            $table->json('payload')->nullable();         // raw snapshot
            $table->uuid('linked_by_user_id')->nullable();
            $table->timestamps();

            $table->foreign('issue_id')->references('id')->on('issues')->cascadeOnDelete();
            $table->foreign('repository_id')->references('id')->on('repositories')->cascadeOnDelete();

            // Avoid duplicate links
            $table->unique(['issue_id', 'repository_id', 'type', 'number', 'name'], 'uniq_issue_vcs');
            $table->index(['repository_id','type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_vcs_links');
    }
};
