<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('issue_codex_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('issue_id')->constrained()->cascadeOnDelete();
            $table->string('codex_page_id');            // Remote Codex page UUID (not a local FK)
            $table->string('codex_page_title');
            $table->string('codex_page_url');
            $table->string('codex_workspace_id');
            $table->string('codex_workspace_slug');
            $table->foreignUuid('added_by_id')->nullable()->nullOnDelete()->constrained('users');
            $table->timestamps();

            $table->unique(['issue_id', 'codex_page_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_codex_links');
    }
};
