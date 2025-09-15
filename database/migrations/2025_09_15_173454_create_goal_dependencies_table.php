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
        Schema::create('goal_dependencies', static function (Blueprint $t) {
            $t->uuid('goal_id');
            $t->uuid('depends_on_goal_id');
            $t->timestamps();

            $t->primary(['goal_id','depends_on_goal_id']);
            $t->foreign('goal_id')->references('id')->on('goals')->cascadeOnDelete();
            $t->foreign('depends_on_goal_id')->references('id')->on('goals')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goal_dependencies');
    }
};
