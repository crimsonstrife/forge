<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates a calendar table for storing events and other calendar-related data, calendars can be specific to users or projects, and user calendars can be shared with other users.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->uuid('uuid')->unique();
            $table->string('name')->default('Calendar');
            $table->string('description')->nullable()->default(null);
            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);
            $table->bigInteger('project_id')->unsigned()->nullable()->default(null);
            $table->boolean('is_project_calendar')->default(false);
            $table->boolean('is_user_calendar')->default(false);
            $table->boolean('is_shared')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('calendars', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
        });

        // Add a unique constraint to prevent duplicate calendars
        Schema::table('calendars', function (Blueprint $table) {
            $table->unique(['user_id', 'slug', 'uuid'], 'unique_user_calendars');
            $table->unique(['project_id', 'slug', 'uuid'], 'unique_project_calendars');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendars');
    }
};
