<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('issue_followers', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('issue_id')->constrained('issues', 'id')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['issue_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_followers');
    }
};
