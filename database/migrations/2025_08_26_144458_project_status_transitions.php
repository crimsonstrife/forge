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
        Schema::create('project_status_transitions', static function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('project_id');
            $t->foreignUuid('from_status_id'); // -> issue_statuses.id
            $t->foreignUuid('to_status_id');   // -> issue_statuses.id
            $t->boolean('is_global')->default(true); // apply to all types unless scoped
            $t->foreignUuid('issue_type_id')->nullable(); // nullable = all types
            $t->timestamps();
            $t->unique(['project_id','from_status_id','to_status_id','issue_type_id'],'proj_transitions_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_status_transitions');
    }
};
