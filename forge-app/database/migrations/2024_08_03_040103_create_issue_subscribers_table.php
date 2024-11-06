<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create a new migration class for the issue subscribers table.
 *
 * This migration will handle the creation of the `issue_subscribers` table
 * in the database. The table will store information about subscribers to
 * specific issues.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('issue_subscribers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('issue_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('issue_subscribers', function (Blueprint $table) {
            $table->foreign('issue_id')->references('id')->on('issues');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_subscribers');
    }
};
