<?php

use App\Enums\MilestoneType;
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
        Schema::create('milestones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->references('id')->on('projects');
            $table->enum('type', MilestoneType::cases());
            $table->enum('state', MilestoneType::cases());
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->dateTime('released_at')->nullable(); // only relevant to Releases
            $table->string('version')->nullable(); // only relevant to Releases
            $table->json('meta')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
