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
        Schema::create('team_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('role_id')->unsigned();
            $table->bigInteger('permission_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('team_role_permissions', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('team_roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('team_permissions')->onDelete('cascade');
        });

        //set constraints
        Schema::table('team_role_permissions', function (Blueprint $table) {
            $table->unique(['role_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_role_permissions');
    }
};
