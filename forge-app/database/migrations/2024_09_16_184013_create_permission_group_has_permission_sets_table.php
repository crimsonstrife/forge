<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


/**
 * Migration class for creating the permission_group_has_permission_sets table.
 * This table establishes a many-to-many relationship between permission groups and permission sets.
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perm_group_has_perm_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_set_id')->constrained()->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perm_group_has_perm_sets');
    }
};
