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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('color')->nullable()->default('#c3c3c3');
            $table->string('icon')->nullable();
            $table->boolean('display_only_on_item_cards')->default(false);
            $table->bigInteger('project_id')->unsigned();
            $table->bigInteger('created_by')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
