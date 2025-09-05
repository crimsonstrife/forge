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
        Schema::create('issue_status_mappings', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('repository_id')->constrained()->cascadeOnDelete();
            $table->string('provider');           // github|gitlab|gitea
            $table->string('external_state');     // e.g., "open", "closed", or a label name
            $table->foreignUuid('issue_status_id')->constrained('issue_statuses')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['repository_id','external_state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_status_mappings');
    }
};
