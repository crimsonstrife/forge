<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration class for creating the permission_group_has_permissions table.
 *
 * This migration will create a table that establishes a many-to-many relationship
 * between permission groups and permissions.
 *
 * The table will include foreign keys referencing the permission_groups and permissions tables.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perm_group_has_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perm_group_has_permissions');
    }
};
