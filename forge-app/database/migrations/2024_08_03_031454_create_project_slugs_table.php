<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create a new anonymous class that extends the Migration class.
 * This class will handle the creation of the 'project_slugs' table in the database.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_slugs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id')->unsigned();
            $table->string('slug')->unique();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('project_slugs', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
        });

        //set constraints
        Schema::table('project_slugs', function (Blueprint $table) {
            $table->unique(['project_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_slugs');
    }
};
