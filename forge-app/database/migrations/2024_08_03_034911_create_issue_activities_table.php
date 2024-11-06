<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create a new migration class for the issue_activities table.
 *
 * This migration will handle the creation of the issue_activities table
 * in the database. The table will be used to store activities related to
 * issues within the application.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('issue_activities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('issue_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('old_status_id')->unsigned();
            $table->bigInteger('new_status_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_activities');
    }
};
