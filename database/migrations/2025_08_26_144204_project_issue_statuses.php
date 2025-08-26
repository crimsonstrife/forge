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
        Schema::create('project_issue_statuses', static function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('project_id');
            $t->foreignUuid('issue_status_id');
            $t->unsignedInteger('order')->default(0);
            $t->boolean('is_initial')->default(false); // starting status
            $t->boolean('is_default_done')->default(false); // optional quick lookup
            $t->timestamps();
            $t->unique(['project_id','issue_status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_issue_statuses');
    }
};
