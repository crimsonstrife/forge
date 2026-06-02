<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_post_issue_links', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('post_id')->index()->constrained('feedback_posts')->cascadeOnDelete();
            $table->foreignUuid('issue_id')->index()->constrained('issues')->cascadeOnDelete();
            $table->foreignUuid('created_by_user_id')->index()->constrained('users')->restrictOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->unique(['post_id', 'issue_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_post_issue_links');
    }
};
