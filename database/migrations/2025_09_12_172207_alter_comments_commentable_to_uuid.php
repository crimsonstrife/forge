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
        Schema::table('comments', static function (Blueprint $table): void {
            // Remove the existing (bigint) morph columns + index
            $table->dropMorphs('commentable');
        });

        Schema::table('comments', static function (Blueprint $table): void {
            // Re-add as UUID morphs: CHAR(36) + type + index
            $table->uuidMorphs('commentable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', static function (Blueprint $table): void {
            $table->dropMorphs('commentable');
        });

        Schema::table('comments', static function (Blueprint $table): void {
            $table->morphs('commentable');
        });
    }
};
