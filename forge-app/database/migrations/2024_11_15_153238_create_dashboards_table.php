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
        Schema::create('dashboards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_shared')->default(false); // Publicly visible
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade'); // Owner of the dashboard
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Creator of the dashboard
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade'); // Last user who updated the dashboard
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('cascade'); // User who deleted the dashboard
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboards');
    }
};
