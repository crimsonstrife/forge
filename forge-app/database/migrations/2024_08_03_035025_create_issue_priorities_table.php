<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create a new migration class for the issue_priorities table.
 *
 * This migration will create the issue_priorities table in the database.
 * The table will be used to store different priority levels for issues.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('issue_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->bigInteger('icon')->unsigned()->nullable();
            $table->string('color')->default('#cecece');
            $table->boolean('is_default')->default(false);
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('issue_priorities', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_priorities');
    }
};
