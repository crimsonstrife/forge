<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_statuses', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('board_id')->index()->constrained('feedback_boards')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('color');
            $table->boolean('is_terminal')->default(false);
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['board_id', 'slug']);
            $table->index(['board_id', 'is_default']);
            $table->index(['board_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_statuses');
    }
};
