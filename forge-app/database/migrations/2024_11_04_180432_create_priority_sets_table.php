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
        Schema::create('priority_sets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Create the foreign key constraints for the priority_sets table
        Schema::table('priority_sets', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
        });

        // Pivot table to associate priorities with sets and include order placement
        Schema::create('issue_priority_priority_set', function (Blueprint $table) {
            $table->id();
            $table->foreignId('priority_set_id')->constrained()->onDelete('cascade');
            $table->foreignId('issue_priority_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0); // Order of priority in the set
            $table->boolean('is_default')->default(false); // Whether this is the default priority for the set
            $table->softDeletes();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->timestamps();
        });

        // Create the foreign key constraints for the issue_priority_priority_set table
        Schema::table('issue_priority_priority_set', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
        });

        // Ensure that priorities are unique within a set, i.e. there can't be two of the same priority in a set
        Schema::table('issue_priority_priority_set', function (Blueprint $table) {
            $table->unique(['priority_set_id', 'issue_priority_id'], 'priority_set_unique');
        });

        // Ensure that the order is unique within a set, i.e. there can't be two priorities with the same order in a set
        Schema::table('issue_priority_priority_set', function (Blueprint $table) {
            $table->unique(['priority_set_id', 'order'], 'priority_order_unique');
        });

        // Ensure only one priority is the default for a set
        Schema::table('issue_priority_priority_set', function (Blueprint $table) {
            $table->unique(['priority_set_id', 'is_default'], 'priority_default_unique');
        });

        // Migration update for Projects
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('priority_set_id')->nullable()->constrained('priority_sets')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraints for the projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['priority_set_id']);
        });

        // Drop the unique constraints for the issue_priority_priority_set table
        Schema::table('issue_priority_priority_set', function (Blueprint $table) {
            $table->dropUnique('priority_set_unique');
            $table->dropUnique('priority_order_unique');
            $table->dropUnique('priority_default_unique');
        });

        // Drop the foreign key constraints for the issue_priority_priority_set table
        Schema::table('issue_priority_priority_set', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);
        });

        // Drop the foreign key constraints for the priority_sets table
        Schema::table('priority_sets', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);
        });

        // Drop the tables
        Schema::dropIfExists('issue_priority_priority_set');
        Schema::dropIfExists('priority_sets');
    }
};
