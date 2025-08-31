<?php

use App\Enums\GoalStatus;
use App\Enums\GoalType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goals', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->morphs('owner'); // owner_type, owner_id -> Organization | Team | Project
            $table->uuid('parent_id')->nullable()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('goal_type')->default(GoalType::Objective->value)->index();
            $table->string('status')->default(GoalStatus::Draft->value)->index();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('progress', 5, 2)->default(0); // cached
            $table->foreignUuid('created_by')->index()->references('id')->on('users');
            $table->json('meta')->nullable(); // SMART details, custom fields
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('goals')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
