<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration class for creating the projects table.
 *
 * This migration will create the projects table in the database.
 * The table will be used to store information about various projects.
 *
 * @return void
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->bigInteger('owner_id')->unsigned();
            $table->bigInteger('status_id')->unsigned();
            $table->bigInteger('type_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('status_id')->references('id')->on('project_statuses');
            $table->foreign('type_id')->references('id')->on('project_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
