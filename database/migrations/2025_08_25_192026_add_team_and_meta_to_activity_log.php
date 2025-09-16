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
        Schema::table('activity_log', static function (Blueprint $table) {
            $table->uuid('team_id')->nullable()->index()->after('id');
            $table->string('ip', 45)->nullable()->after('description');
            $table->string('user_agent', 255)->nullable()->after('ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log', static function (Blueprint $table) {
            $table->dropColumn(['team_id','ip','user_agent']);
        });
    }
};
