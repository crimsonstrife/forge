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
        Schema::create('issues', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained('projects', 'id');
            $table->foreignId('issue_type_id');
            $table->foreignId('issue_status_id');
            $table->foreignId('issue_priority_id');
            $table->foreignUuid('reporter_id')->nullable()->constrained('users', 'id')->nullOnDelete();
            $table->foreignUuid('assignee_id')->nullable()->constrained('users', 'id')->nullOnDelete();
            $table->integer('story_points')->nullable();
            $table->integer('estimate_minutes')->nullable();
            $table->longText('description')->nullable();
            $table->text('summary')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
