<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', static function (Blueprint $t): void {
            $t->uuid('id')->primary();
            $t->foreignUuid('user_id')->constrained('users');
            $t->foreignUuid('issue_id')->nullable()->constrained('issues');
            $t->string('title')->nullable();
            $t->text('body')->nullable();
            $t->json('tags')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
