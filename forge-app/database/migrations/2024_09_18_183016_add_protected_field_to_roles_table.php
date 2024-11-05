<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


/**
 * Migration class to add a protected field to the roles table.
 *
 * This migration will modify the roles table by adding a new field
 * that is protected. The exact details of the field (such as its
 * name, type, and constraints) will be defined within the migration
 * methods.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('protected')->default(false); // Protect roles from being deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('protected');
        });
    }
};
