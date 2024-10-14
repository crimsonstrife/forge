<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discord_config', function (Blueprint $table) {
            $table->id()->default(1); // Force the primary key to always be 1
            $table->boolean('enabled')->default(false); // Enable or disable the Discord connectivity feature
            $table->string('guild_id')->unique();  // Ensure only one Discord server is allowed
            $table->string('client_id')->unique();
            $table->string('client_secret');
            $table->string('bot_token');
            $table->string('redirect_uri');
            $table->json('role_mappings')->nullable();    // Store role mappings
            $table->json('channel_mappings')->nullable(); // Store channel mappings
            $table->timestamps();

            // Ensure that there can only ever be one row in the table
            $table->unique('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discord_config');
    }
};
