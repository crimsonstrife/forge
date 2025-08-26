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
        Schema::create('issue_external_refs', function (Blueprint $table) {
            $table->uuid();
            $table->foreignUuid('issue_id')->constrained('issues', 'id');
            $table->string('provider')->default('github');
            $table->string('external_id');
            $table->unique(['provider', 'external_id']);
            $table->boolean('read_only')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_external_refs');
    }
};
