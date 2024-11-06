<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateIssueSlugsTable Migration
 *
 * This migration class is responsible for creating the 'issue_slugs' table in the database.
 * The table is used to store slugs for issues, which are unique identifiers that can be used
 * in URLs or other contexts where a human-readable identifier is needed.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('issue_slugs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->bigInteger('issue_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('issue_slugs', function (Blueprint $table) {
            $table->foreign('issue_id')->references('id')->on('issues');
        });

        //set constraints
        Schema::table('issue_slugs', function (Blueprint $table) {
            $table->unique(['issue_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_slugs');
    }
};
