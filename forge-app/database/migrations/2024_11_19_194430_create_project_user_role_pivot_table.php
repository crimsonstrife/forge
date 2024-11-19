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
        Schema::create('project_user_role_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_role_id')->constrained('project_roles')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Constraint to ensure that a user can't have the same role more than once
        Schema::table('project_user_role_pivot', function (Blueprint $table) {
            $table->unique(['project_role_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_user_role_pivot');
    }
};
