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
