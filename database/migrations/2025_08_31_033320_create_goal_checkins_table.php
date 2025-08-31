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
        Schema::create('goal_checkins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('goal_key_result_id')->index();
            $table->uuid('created_by')->index();
            $table->decimal('value', 16, 4);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('goal_key_result_id')->references('id')->on('goal_key_results')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goal_checkins');
    }
};
