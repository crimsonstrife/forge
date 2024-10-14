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
        Schema::create('icons', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Icon name
            $table->string('type'); // Icon type: heroicon, fontawesome, custom, etc.
            $table->enum('style', ['solid', 'regular', 'light', 'duotone', 'brands', 'outline'])->default('solid'); // Icon style
            $table->text('svg_code')->nullable(); // Optional for custom SVGs entered as text
            $table->string('svg_file_path')->nullable(); // Store the uploaded SVG file path
            $table->bigInteger('created_by')->unsigned()->nullable(); // User ID who created the icon
            $table->bigInteger('updated_by')->unsigned()->nullable(); // User ID who updated the icon
            $table->bigInteger('deleted_by')->unsigned()->nullable(); // User ID who deleted the icon
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('icons', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
        });

        // Add a unique constraint for the icon name, type, and style
        Schema::table('icons', function (Blueprint $table) {
            $table->unique(['name', 'type', 'style']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('icons');
    }
};
