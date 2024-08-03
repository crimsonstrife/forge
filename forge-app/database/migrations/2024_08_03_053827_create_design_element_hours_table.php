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
        Schema::create('design_element_hours', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('design_element_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->float('value');
            $table->timestamps();
        });

        Schema::table('design_element_hours', function (Blueprint $table) {
            $table->foreign('design_element_id')->references('id')->on('design_elements');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_element_hours');
    }
};
