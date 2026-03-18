<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('user_tour_states', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('tour');
            $table->string('status')->default('pending');
            $table->unsignedInteger('last_step')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('snoozed_until')->nullable();
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'tour']);
            $table->index(['tour', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tour_states');
    }
};
