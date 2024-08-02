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
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('permission_group_permission', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('permission_group_id')->unsigned();
            $table->bigInteger('permission_id')->unsigned();
            $table->timestamps();
        });

        // user_permission_group migration
        Schema::create('user_permission_group', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('permission_group_id')->unsigned();
            $table->timestamps();
        });

        // role_permission_group migration
        Schema::create('role_permission_group', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('role_id')->unsigned();
            $table->bigInteger('permission_group_id')->unsigned();
            $table->timestamps();
        });

        // team_permission_group migration (if applicable)
        Schema::create('team_permission_group', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('team_id')->unsigned();
            $table->bigInteger('permission_group_id')->unsigned();
            $table->timestamps();
        });

        // Add foreign keys
        Schema::table('permission_group_permission', function (Blueprint $table) {
            $table->foreign('permission_group_id')->references('id')->on('permission_groups')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });

        Schema::table('user_permission_group', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('permission_group_id')->references('id')->on('permission_groups')->onDelete('cascade');
        });

        Schema::table('role_permission_group', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_group_id')->references('id')->on('permission_groups')->onDelete('cascade');
        });

        Schema::table('team_permission_group', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('permission_group_id')->references('id')->on('permission_groups')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_groups');
        Schema::dropIfExists('permission_group_permission');
        Schema::dropIfExists('user_permission_group');
        Schema::dropIfExists('role_permission_group');
        Schema::dropIfExists('team_permission_group');
    }
};
