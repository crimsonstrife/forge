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
        Schema::table('issues', function (Blueprint $table) {
            $table->dropColumn('milestone_id'); //drop existing bigint that was unused
            $table->foreignUuid('milestone_id')->nullable()->references('id', 'milestone_id_project_index')->on('milestones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropForeign('milestone_id_project_index');
            $table->unsignedBigInteger('milestone_id')->nullable();
        });
    }
};
