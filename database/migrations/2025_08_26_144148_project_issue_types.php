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
        Schema::create('project_issue_types', static function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('project_id');
            $t->foreignId('issue_type_id');
            $t->unsignedInteger('order')->default(0);
            $t->boolean('is_default')->default(false);
            $t->timestamps();
            $t->unique(['project_id','issue_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_issue_types');
    }
};
