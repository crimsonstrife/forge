<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stores basic identifying information about a repository pull from the Crucible API.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('repository', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('remote_id')->unsigned()->unique();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('http_url');
            $table->string('ssh_url');
            $table->string('scm');
            $table->json('history');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repository');
    }
};
