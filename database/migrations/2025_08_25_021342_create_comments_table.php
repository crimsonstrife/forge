<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users');

            // Polymorphic target (root container: Issue, Attachment, etc.)
            $table->nullableMorphs('commentable'); // commentable_type (string), commentable_id (uuid or string)

            // Threading
            $table->uuid('parent_id')->nullable()->index();
            $table->foreign('parent_id')->references('id')->on('comments')->cascadeOnDelete();

            // file-thread context (Spatie Media uses BIGINT IDs)
            $table->unsignedBigInteger('context_media_id')->nullable()->index();

            $table->longText('body');

            // Fast filtering
            $table->index(['commentable_type', 'commentable_id']);
            $table->index(['parent_id', 'created_at']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
