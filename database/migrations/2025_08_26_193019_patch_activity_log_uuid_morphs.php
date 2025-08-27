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
        Schema::table('activity_log', static function (Blueprint $table) {
            $table->uuid('subject_id')->nullable()->change();
            $table->uuid('causer_id')->nullable()->change();
            $table->uuid('team_id')->nullable()->change();
            $table->uuid('batch_uuid')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log', static function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable()->change();
            $table->unsignedBigInteger('causer_id')->nullable()->change();
            $table->unsignedBigInteger('team_id')->nullable()->change();
            $table->char('batch_uuid', 36)->nullable()->change();
        });
    }
};
