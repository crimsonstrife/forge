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
        Schema::table('goals', static function (Blueprint $t) {
            if (!Schema::hasColumn('goals', 'cycle_label')) {
                $t->string('cycle_label')->nullable()->index();
            }
            if (!Schema::hasColumn('goals', 'cycle_start')) {
                $t->date('cycle_start')->nullable();
            }
            if (!Schema::hasColumn('goals', 'cycle_end')) {
                $t->date('cycle_end')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', static function (Blueprint $t) {
            if (Schema::hasColumn('goals', 'cycle_label')) {
                $t->dropColumn('cycle_label');
            }
            if (Schema::hasColumn('goals', 'cycle_start')) {
                $t->dropColumn('cycle_start');
            }
            if (Schema::hasColumn('goals', 'cycle_end')) {
                $t->dropColumn('cycle_end');
            }
        });
    }
};
