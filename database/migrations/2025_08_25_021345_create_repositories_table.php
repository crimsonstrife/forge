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
        Schema::create('repositories', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider');               // github|gitlab|gitea|crucible
            $table->string('host')->default('github.com'); // e.g., github.com, gitlab.com, gitea.mycorp.com
            $table->string('owner');                  // org/user
            $table->string('name');                   // repo name
            $table->string('external_id')->nullable();// repo id from provider
            $table->string('default_branch')->nullable();
            $table->json('meta')->nullable();         // misc provider data
            $table->timestamps();
            $table->unique(['provider', 'host', 'owner', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repositories');
    }
};
