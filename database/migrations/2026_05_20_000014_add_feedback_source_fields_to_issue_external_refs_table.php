<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('issue_external_refs', static function (Blueprint $table): void {
            $table->string('source_type')->nullable()->after('issue_id');
            $table->string('source_id')->nullable()->after('source_type');
            $table->unique(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::table('issue_external_refs', static function (Blueprint $table): void {
            $table->dropUnique(['source_type', 'source_id']);
            $table->dropColumn(['source_type', 'source_id']);
        });
    }
};
