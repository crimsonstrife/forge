<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_issue_views', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('team_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('query')->nullable();
            $table->json('filters')->nullable();
            $table->string('sort', 40)->default('updated_desc');
            $table->boolean('is_shared')->default(false);
            $table->timestamps();
            $table->index(['team_id', 'is_shared']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_issue_views');
    }
};
