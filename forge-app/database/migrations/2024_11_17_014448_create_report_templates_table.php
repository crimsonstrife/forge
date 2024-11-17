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
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id();
            $table->string(column: 'name');
            $table->text('description')->nullable();
            $table->json('content')->nullable(); // Store the report content as JSON for faster access/styling
            $table->json('settings')->nullable(); // Store additional report-specific settings
            $table->json('filters')->nullable(); // Store additional report-specific filters
            $table->foreignId('dashboard_id')->constrained('dashboards')->onDelete('cascade');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        // Add the template_id column to the reports table
        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('template_id')->nullable()->constrained('report_templates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_templates');
    }
};
