<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates a shared_calendars table for storing shared calendars between users, a calendar can be shared with multiple users, but not with the same user more than once.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shared_calendars', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('calendar_id')->unsigned();
            $table->bigInteger('owner_user_id')->unsigned();
            $table->bigInteger('shared_user_id')->unsigned();
            $table->date('shared_at')->nullable()->default(null);
            $table->date('expires_at')->nullable()->default(null);
            $table->boolean('is_accepted')->default(false);
            $table->boolean('is_expired')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('shared_calendars', function (Blueprint $table) {
            $table->foreign('calendar_id')->references('id')->on('calendars');
            $table->foreign('owner_user_id')->references('id')->on('users');
            $table->foreign('shared_user_id')->references('id')->on('users');
        });

        // Add a unique constraint to prevent the same calendar from being shared with a user more than once unless it is expired
        Schema::table('shared_calendars', function (Blueprint $table) {
            $table->unique(['calendar_id', 'shared_user_id'], 'unique_shared_calendar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_calendars');
    }
};
