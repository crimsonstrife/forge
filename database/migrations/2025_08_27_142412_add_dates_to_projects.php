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
        Schema::table('projects', static function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('stage');
            }
            if (! Schema::hasColumn('projects', 'due_at')) {
                $table->timestamp('due_at')->nullable()->after('started_at'); // explicit “deadline”
            }
            if (! Schema::hasColumn('projects', 'ended_at')) {
                $table->timestamp('ended_at')->nullable()->after('due_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', static function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'started_at')) { $table->dropColumn('started_at'); }
            if (Schema::hasColumn('projects', 'due_at'))     { $table->dropColumn('due_at'); }
            if (Schema::hasColumn('projects', 'ended_at'))   { $table->dropColumn('ended_at'); }
        });
    }
};
