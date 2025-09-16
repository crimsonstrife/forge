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
        Schema::create('issue_links', static function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('issue_link_type_id');
            $table->uuid('from_issue_id'); // "from" issue performs the 'name' action on the "to" issue
            $table->uuid('to_issue_id');   // target issue

            $table->uuid('created_by_id')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->foreign('issue_link_type_id')->references('id')->on('issue_link_types')->cascadeOnDelete();
            $table->foreign('from_issue_id')->references('id')->on('issues')->cascadeOnDelete();
            $table->foreign('to_issue_id')->references('id')->on('issues')->cascadeOnDelete();
            $table->foreign('created_by_id')->references('id')->on('users')->nullOnDelete();

            // Useful lookup indexes
            $table->index('from_issue_id');
            $table->index('to_issue_id');

            // Canonical pair columns to prevent duplicates in either direction (MySQL 8+ virtual columns)
            $table->string('canonical_a')->virtualAs("IF(`from_issue_id` < `to_issue_id`, `from_issue_id`, `to_issue_id`)");
            $table->string('canonical_b')->virtualAs("IF(`from_issue_id` < `to_issue_id`, `to_issue_id`, `from_issue_id`)");

            // Only one link per type between two issues (regardless of direction)
            $table->unique(['issue_link_type_id', 'canonical_a', 'canonical_b'], 'issue_links_unique_pair');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_links');
    }
};
