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
        Schema::create('discord_config', function (Blueprint $table) {
            $table->id();
            $table->string('guild_id');  // Ensure only one Discord server is allowed
            $table->string('client_id');
            $table->string('client_secret');
            $table->string('bot_token');
            $table->string('redirect_uri');
            $table->json('role_mappings')->nullable();    // Store role mappings
            $table->json('channel_mappings')->nullable(); // Store channel mappings
            $table->timestamps();
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
