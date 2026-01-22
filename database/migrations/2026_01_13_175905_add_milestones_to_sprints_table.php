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
        Schema::table('sprints', function (Blueprint $table): void {
            if (Schema::hasColumn('sprints', 'milestone_id')) {
                $table->dropColumn('milestone_id');
            }

            $table->foreignUuid('milestone_id')
                ->nullable()
                ->constrained('milestones')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sprints', function (Blueprint $table): void {
            $table->dropForeign(['milestone_id']);
            $table->dropColumn('milestone_id');
        });
    }
};
