<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateProjectFavoritesTable migration class.
 *
 * This migration creates the 'project_favorites' table in the database.
 * The table is used to store information about user favorite projects.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_favorites', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('project_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('project_favorites', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
        });

        //set constraints
        Schema::table('project_favorites', function (Blueprint $table) {
            $table->unique(['user_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_favorites');
    }
};
