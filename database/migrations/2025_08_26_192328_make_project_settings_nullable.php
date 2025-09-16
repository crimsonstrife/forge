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
        Schema::table('projects', static function (Blueprint $table) {
            $table->json('settings')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to NOT NULL TODO:(will fail if rows contain NULLs)
        Schema::table('projects', static function (Blueprint $table) {
            $table->json('settings')->nullable(false)->change();
        });

    }
};
