<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('issues', static function (Blueprint $t): void {
            $t->boolean('is_next')->default(false)->after('estimate_minutes');
            $t->index(['project_id', 'is_next', 'updated_at'], 'issues_project_next_updated');
        });
    }

    public function down(): void
    {
        Schema::table('issues', static function (Blueprint $t): void {
            $t->dropIndex('issues_project_next_updated');
            $t->dropColumn('is_next');
        });
    }
};
