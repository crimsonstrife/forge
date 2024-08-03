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
        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('slug')->unique();
            $table->uuid('uuid')->unique();
            $table->enum('type', ['kanban', 'gantt']);
            $table->boolean('is_public')->default(false);
            $table->bigInteger('project_id')->unsigned();
            $table->bigInteger('created_by')->unsigned();
            $table->timestamps();
        });

        Schema::table('boards', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boards');
    }
};
