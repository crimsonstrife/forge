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
        Schema::create('issue_slugs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->bigInteger('issue_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('issue_slugs', function (Blueprint $table) {
            $table->foreign('issue_id')->references('id')->on('issues');
        });

        //set constraints
        Schema::table('issue_slugs', function (Blueprint $table) {
            $table->unique(['issue_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_slugs');
    }
};
