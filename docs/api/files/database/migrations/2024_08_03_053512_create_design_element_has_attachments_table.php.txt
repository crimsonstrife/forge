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
        Schema::create('design_element_has_attachments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('design_element_id')->unsigned();
            $table->bigInteger('attachment_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('design_element_has_attachments', function (Blueprint $table) {
            $table->foreign('design_element_id')->references('id')->on('design_elements');
            $table->foreign('attachment_id')->references('id')->on('attachments');
            $table->foreign('user_id')->references('id')->on('users');
        });

        //set unique constraint
        Schema::table('design_element_has_attachments', function (Blueprint $table) {
            $table->unique(['design_element_id', 'attachment_id'], 'design_element_has_attachments_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_element_has_attachments');
    }
};
