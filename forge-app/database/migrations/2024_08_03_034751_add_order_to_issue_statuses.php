<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to add an 'order' column to the 'issue_statuses' table.
 *
 * This migration will modify the 'issue_statuses' table by adding a new column
 * to store the order of the issue statuses.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('issue_statuses', function (Blueprint $table) {
            $table->integer('order')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issue_statuses', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
