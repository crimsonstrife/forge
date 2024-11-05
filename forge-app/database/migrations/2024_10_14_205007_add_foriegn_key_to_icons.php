<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


/**
 * Migration class to add a foreign key to the icons table.
 *
 * This migration will add a foreign key constraint to the specified column
 * in the icons table to ensure referential integrity.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adds a foreign key to the tables that reference the icons table
        Schema::table('issue_types', function (Blueprint $table) {
            $table->foreign('icon')->nullable()->references('id')->on('icons')->onDelete('set null');
        });
        Schema::table('issue_priorities', function (Blueprint $table) {
            $table->foreign('icon')->nullable()->references('id')->on('icons')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issue_types', function (Blueprint $table) {
            $table->dropForeign(['icon']);
        });
        Schema::table('issue_priorities', function (Blueprint $table) {
            $table->dropForeign(['icon']);
        });
    }
};
