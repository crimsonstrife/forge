<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration class to modify the issue_relations table and add a foreign key.
 *
 * This migration will be executed to update the database schema by adding a foreign key constraint
 * to the issue_relations table. The specific changes and the foreign key details should be defined
 * within the up() and down() methods of this class.
 *
 * @return void
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('issue_relations', function (Blueprint $table) {
            $table->foreign('issue_relation_type')->references('id')->on('model_relation_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issue_relations', function (Blueprint $table) {
            $table->dropForeign(['issue_relation_type']);
        });
    }
};
