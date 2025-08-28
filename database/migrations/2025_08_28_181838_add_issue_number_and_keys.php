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
        Schema::table('projects', static function (Blueprint $t) {
            if (! Schema::hasColumn('projects', 'next_issue_number')) {
                $t->unsignedBigInteger('next_issue_number')->default(1)->after('key');
            }
        });

        Schema::table('issues', static function (Blueprint $t) {
            if (! Schema::hasColumn('issues', 'number')) {
                $t->unsignedBigInteger('number')->nullable()->after('project_id');
                $t->index(['project_id', 'number']); // fast lookups
                $t->unique(['project_id', 'number']); // per-project uniqueness
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', static function (Blueprint $t) {
            if (Schema::hasColumn('issues', 'number')) {
                $t->dropUnique(['project_id','number']);
                $t->dropIndex(['project_id','number']);
                $t->dropColumn('number');
            }
        });

        Schema::table('projects', static function (Blueprint $t) {
            if (Schema::hasColumn('projects', 'next_issue_number')) {
                $t->dropColumn('next_issue_number');
            }
        });
    }
};
