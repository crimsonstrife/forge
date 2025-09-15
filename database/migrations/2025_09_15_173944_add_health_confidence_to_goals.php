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
        Schema::table('goals', function (Blueprint $t) {
            if (!Schema::hasColumn('goals', 'confidence')) {
                $t->unsignedTinyInteger('confidence')->default(70);
            }
            if (!Schema::hasColumn('goals', 'health')) {
                $t->string('health')->default('on_track')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', function (Blueprint $t) {
            if (Schema::hasColumn('goals', 'confidence')) {
                $t->dropColumn('confidence');
            }
            if (Schema::hasColumn('goals', 'health')) {
                $t->dropColumn('health');
            }
        });
    }
};
