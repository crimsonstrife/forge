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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('description')->nullable()->default(null);
            $table->bigInteger('owner_user_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->foreign('owner_user_id')->references('id')->on('users');
        });

        // Add a unique constraint to prevent duplicate teams
        Schema::table('teams', function (Blueprint $table) {
            $table->unique(['owner_user_id', 'slug', 'uuid'], 'unique_user_teams');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
