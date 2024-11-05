<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


/**
 * Migration class for creating the pending_user_emails table.
 *
 * This migration will create a table to store email addresses of users
 * who have not yet completed the registration process.
 *
 * Class CreatePendingUserEmailsTable
 *
 * @return void
 */
class CreatePendingUserEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_user_emails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('user');
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_user_emails');
    }
}
