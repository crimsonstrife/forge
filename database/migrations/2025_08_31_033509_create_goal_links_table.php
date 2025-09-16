<?php

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
        Schema::create('goal_links', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('goal_id')->index();
            $table->morphs('linkable'); // Issues, Projects, etc.
            $table->unsignedInteger('weight')->default(1);
            $table->timestamps();

            $table->foreign('goal_id')->references('id')->on('goals')->cascadeOnDelete();
            $table->unique(['goal_id','linkable_type','linkable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goal_links');
    }
};
