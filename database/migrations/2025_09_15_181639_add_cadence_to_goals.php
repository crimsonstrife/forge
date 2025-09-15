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
        Schema::table('goals', static function (Blueprint $t) {
            if (!Schema::hasColumn('goals', 'cadence')) {
                $t->string('cadence')->default('weekly');
            }
            if (!Schema::hasColumn('goals', 'next_checkin_at')) {
                $t->timestamp('next_checkin_at')->nullable()->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', static function (Blueprint $t) {
            if (Schema::hasColumn('goals', 'cadence')) {
                $t->dropColumn('cadence');
            }
            if (Schema::hasColumn('goals', 'next_checkin_at')) {
                $t->dropColumn('next_checkin_at');
            }
        });
    }
};
