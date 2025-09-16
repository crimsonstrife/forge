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
        Schema::table('issues', function (Blueprint $table) {
            $table->timestampTz('starts_at')->nullable()->after('description');
            $table->timestampTz('due_at')->nullable()->after('starts_at');
            $table->unsignedBigInteger('milestone_id')->nullable()->after('due_at');

            $table->index(['project_id', 'starts_at']);
            $table->index(['project_id', 'due_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table): void {
            $table->dropIndex(['project_id', 'starts_at']);
            $table->dropIndex(['project_id', 'due_at']);
            $table->dropColumn(['starts_at', 'due_at', 'milestone_id']);
        });
    }
};
