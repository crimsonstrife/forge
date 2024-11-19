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
        Schema::create('project_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('role_id')->unsigned();
            $table->bigInteger('permission_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('project_role_permissions', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('project_roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('project_permissions')->onDelete('cascade');
        });

        // Constraint to ensure that a role can't have the same permission more than once
        Schema::table('project_role_permissions', function (Blueprint $table) {
            $table->unique(['role_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_role_permissions');
    }
};
