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
        Schema::create('issue_external_refs', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('issue_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('repository_id')->constrained()->cascadeOnDelete();
            $table->string('provider');               // github|gitlab|gitea|crucible
            $table->string('external_issue_id');      // provider issue id
            $table->unsignedBigInteger('number');     // issue number (#123)
            $table->string('url')->nullable();
            $table->string('state')->nullable();      // open|closed|...
            $table->json('payload')->nullable();      // last seen payload
            $table->timestamps();
            $table->unique(['repository_id','external_issue_id']);
            $table->unique(['repository_id','number']); // fast lookup by #number
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_external_refs');
    }
};
