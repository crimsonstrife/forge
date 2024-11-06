<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateProjectTypesTable migration class.
 *
 * This migration creates the 'project_types' table in the database.
 * It is part of the Forge application and is located in the
 * 'database/migrations' directory.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->default('#cecece');
            $table->string('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_types');
    }
};
