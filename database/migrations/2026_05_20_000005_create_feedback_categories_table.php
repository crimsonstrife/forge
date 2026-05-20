<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_categories', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('board_id')->index()->constrained('feedback_boards')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('color')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['board_id', 'slug']);
            $table->index(['board_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_categories');
    }
};
