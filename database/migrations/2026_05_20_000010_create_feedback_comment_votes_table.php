<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_comment_votes', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('comment_id')->index()->constrained('feedback_comments')->cascadeOnDelete();
            $table->foreignUlid('identity_id')->index()->constrained('feedback_identities')->restrictOnDelete();
            $table->tinyInteger('value');
            $table->timestamps();

            $table->unique(['comment_id', 'identity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_comment_votes');
    }
};
