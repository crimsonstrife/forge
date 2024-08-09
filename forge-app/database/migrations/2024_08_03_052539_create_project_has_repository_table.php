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
        Schema::create('project_has_repository', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id')->unsigned();
            $table->bigInteger('repository_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('project_has_repository', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('repository_id')->references('id')->on('repository');
        });

        //set unique constraint
        Schema::table('project_has_repository', function (Blueprint $table) {
            $table->unique(['project_id', 'repository_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_has_repository');
    }
};
