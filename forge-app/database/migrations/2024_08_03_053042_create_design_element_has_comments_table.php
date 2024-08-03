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
        Schema::create('design_element_has_comments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('design_element_id')->unsigned();
            $table->bigInteger('comment_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('design_element_has_comments', function (Blueprint $table) {
            $table->foreign('design_element_id')->references('id')->on('design_elements');
            $table->foreign('comment_id')->references('id')->on('comment');
        });

        //set unique constraint
        Schema::table('design_element_has_comments', function (Blueprint $table) {
            $table->unique(['design_element_id', 'comment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_element_has_comments');
    }
};
