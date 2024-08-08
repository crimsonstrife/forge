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
        Schema::create('issue_has_attachments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('issue_id')->unsigned();
            $table->bigInteger('attachment_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('issue_has_attachments', function (Blueprint $table) {
            $table->foreign('issue_id')->references('id')->on('issues');
            $table->foreign('attachment_id')->references('id')->on('attachments');
            $table->foreign('user_id')->references('id')->on('users');
        });

        //set unique constraint
        Schema::table('issue_has_attachments', function (Blueprint $table) {
            $table->unique(['issue_id', 'attachment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_has_attachments');
    }
};
