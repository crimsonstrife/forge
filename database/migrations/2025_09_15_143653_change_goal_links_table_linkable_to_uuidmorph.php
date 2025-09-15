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
        Schema::table('goal_links', static function (Blueprint $table) {
            // Remove the existing (bigint) morph columns + index
            $table->dropMorphs('linkable');
        });

        Schema::table('goal_links', static function (Blueprint $table): void {
            // Re-add as UUID morphs: CHAR(36) + type + index
            $table->uuidMorphs('linkable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goal_links', static function (Blueprint $table) {
            $table->dropMorphs('linkable');
        });

        Schema::table('goal_links', static function (Blueprint $table): void {
            $table->morphs('linkable');
        });
    }
};
