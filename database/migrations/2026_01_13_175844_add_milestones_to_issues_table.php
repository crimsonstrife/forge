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
        Schema::table('issues', function (Blueprint $table) {
            if (Schema::hasColumn('issues', 'milestone_id')) {
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
        Schema::table('issues', function (Blueprint $table) {
            Schema::table('issues', function (Blueprint $table): void {
                $table->dropForeign(['milestone_id']);
                $table->dropColumn('milestone_id');

                // If you truly need the old type back (bigint), restore it here:
                // $table->unsignedBigInteger('milestone_id')->nullable();
            });
        });
    }
};
