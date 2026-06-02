<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_post_votes', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('post_id')->index()->constrained('feedback_posts')->cascadeOnDelete();
            $table->foreignUlid('identity_id')->index()->constrained('feedback_identities')->restrictOnDelete();
            $table->tinyInteger('value');
            $table->timestamps();

            $table->unique(['post_id', 'identity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_post_votes');
    }
};
