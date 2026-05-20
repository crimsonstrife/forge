<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_posts', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('key', 32)->unique();
            $table->foreignUlid('board_id')->index()->constrained('feedback_boards')->cascadeOnDelete();
            $table->foreignUlid('identity_id')->index()->constrained('feedback_identities')->restrictOnDelete();
            $table->foreignUlid('status_id')->index()->constrained('feedback_statuses')->restrictOnDelete();
            $table->foreignUlid('category_id')->nullable()->index()->constrained('feedback_categories')->nullOnDelete();
            $table->string('title', 200);
            $table->longText('body');
            $table->longText('body_html');
            $table->unsignedInteger('upvote_count')->default(0);
            $table->unsignedInteger('downvote_count')->default(0);
            $table->integer('net_score')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->double('trending_score')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('pinned_at')->nullable();
            $table->foreignUlid('merged_into_post_id')->nullable()->index()->constrained('feedback_posts')->nullOnDelete();
            $table->timestamp('last_activity_at')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['board_id', 'is_pinned', 'net_score']);
            $table->index(['board_id', 'status_id', 'created_at']);
            $table->index(['board_id', 'trending_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_posts');
    }
};
