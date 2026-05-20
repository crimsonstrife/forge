<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_comments', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('post_id')->index()->constrained('feedback_posts')->cascadeOnDelete();
            $table->foreignUlid('identity_id')->nullable()->index()->constrained('feedback_identities')->restrictOnDelete();
            $table->foreignUuid('staff_user_id')->nullable()->index()->constrained('users')->restrictOnDelete();
            $table->foreignUlid('parent_comment_id')->nullable()->index()->constrained('feedback_comments')->cascadeOnDelete();
            $table->longText('body');
            $table->longText('body_html');
            $table->unsignedInteger('upvote_count')->default(0);
            $table->unsignedInteger('downvote_count')->default(0);
            $table->integer('net_score')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_staff_reply')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['post_id', 'parent_comment_id', 'created_at']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE feedback_comments ADD CONSTRAINT feedback_comments_one_author CHECK ((identity_id IS NULL) <> (staff_user_id IS NULL))');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_comments');
    }
};
