<?php

use App\Enums\MilestoneState;
use App\Enums\MilestoneType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', array_map(
                static fn (MilestoneType $case): string => $case->value,
                MilestoneType::cases(),
            ))->index();

            $table->enum('state', array_map(
                static fn (MilestoneState $case): string => $case->value,
                MilestoneState::cases(),
            ))->index();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('due_at')->nullable();

            $table->string('version')->nullable();
            $table->timestamp('released_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
