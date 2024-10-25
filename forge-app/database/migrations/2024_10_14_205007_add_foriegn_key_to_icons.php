<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adds a foreign key to the tables that reference the icons table
        Schema::table('issue_types', function (Blueprint $table) {
            $table->foreign('icon')->nullable()->references('id')->on('icons')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issue_types', function (Blueprint $table) {
            $table->dropForeign(['icon']);
        });
    }
};
