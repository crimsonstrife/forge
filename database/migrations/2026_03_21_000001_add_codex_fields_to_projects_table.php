<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('codex_workspace_id')->nullable()->index()->after('settings');
            $table->string('codex_workspace_slug')->nullable()->after('codex_workspace_id');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['codex_workspace_id', 'codex_workspace_slug']);
        });
    }
};
