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
        Schema::table('issue_types', static function (Blueprint $table) {
            $table->boolean('is_hierarchical')->default(false);
            $table->string('key')->unique(); // e.g. EPIC, STORY, BUG
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issue_types', static function (Blueprint $table) {
            $table->dropColumn('key');
            $table->dropColumn('is_hierarchical');
        });
    }
};
