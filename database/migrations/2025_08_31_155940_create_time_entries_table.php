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
        Schema::create('time_entries', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('issue_id');
            $table->uuid('user_id');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0); // finalized at stop
            $table->string('source', 20)->default('timer'); // timer|manual|import
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['issue_id', 'user_id']);
            $table->index('started_at');
            $table->index('ended_at');
            $table->foreign('issue_id')->references('id')->on('issues')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
