<?php

use App\Enums\KRAutomation;
use App\Enums\KRDirection;
use App\Enums\MetricUnit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goal_key_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('goal_id')->index();
            $table->string('name');
            $table->string('unit')->default(MetricUnit::Percent->value);
            $table->string('direction')->default(KRDirection::IncreaseTo->value);
            $table->decimal('initial_value', 16, 4)->default(0);
            $table->decimal('current_value', 16, 4)->default(0);
            $table->decimal('target_min', 16, 4)->nullable();
            $table->decimal('target_max', 16, 4)->nullable();
            $table->decimal('target_value', 16, 4)->nullable();
            $table->string('automation')->default(KRAutomation::Manual->value);
            $table->unsignedInteger('weight')->default(1);
            $table->timestamps();

            $table->foreign('goal_id')->references('id')->on('goals')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goal_key_results');
    }
};
