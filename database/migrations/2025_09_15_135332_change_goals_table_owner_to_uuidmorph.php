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
        Schema::table('goals', static function (Blueprint $table) {
            // Remove the existing (bigint) morph columns + index
            $table->dropMorphs('owner');
        });

        Schema::table('goals', static function (Blueprint $table): void {
            // Re-add as UUID morphs: CHAR(36) + type + index
            $table->uuidMorphs('owner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', static function (Blueprint $table) {
            $table->dropMorphs('owner');
        });

        Schema::table('goals', static function (Blueprint $table): void {
            $table->morphs('owner');
        });
    }
};
