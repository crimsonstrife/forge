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
        Schema::create('issue_has_tags', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('issue_id')->unsigned();
            $table->bigInteger('tag_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('issue_has_tags', function (Blueprint $table) {
            $table->foreign('issue_id')->references('id')->on('issues');
            $table->foreign('tag_id')->references('id')->on('tags');
        });

        //set unique constraint
        Schema::table('issue_has_tags', function (Blueprint $table) {
            $table->unique(['issue_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_has_tags');
    }
};
