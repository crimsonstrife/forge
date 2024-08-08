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
        Schema::table('stories', function (Blueprint $table) {
            $table->bigInteger('epic_id')->unsigned()->nullable();
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->foreign('epic_id')->references('id')->on('epics');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->dropForeign(['epic_id']);
            $table->dropColumn('epic_id');
        });
    }
};
