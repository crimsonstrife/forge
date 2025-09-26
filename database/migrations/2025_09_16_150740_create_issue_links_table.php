<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('issue_links', static function (Blueprint $table): void {
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

            // Helpful lookups
            $table->index('from_issue_id');
            $table->index('to_issue_id');

            // Canonical pair (direction-agnostic), driver-neutral CASE expression
            $caseA = 'CASE WHEN from_issue_id < to_issue_id THEN from_issue_id ELSE to_issue_id END';
            $caseB = 'CASE WHEN from_issue_id < to_issue_id THEN to_issue_id ELSE from_issue_id END';

            $table->uuid('canonical_a')->virtualAs($caseA);
            $table->uuid('canonical_b')->virtualAs($caseB);

            // Only one link per type between two issues (regardless of direction)
            $table->unique(['issue_link_type_id', 'canonical_a', 'canonical_b'], 'issue_links_unique_pair');

            // Optional: prevent self-links
            $table->check('from_issue_id <> to_issue_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_links');
    }
};
