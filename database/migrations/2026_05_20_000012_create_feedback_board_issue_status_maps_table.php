<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_board_issue_status_maps', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('board_id')->index()->constrained('feedback_boards')->cascadeOnDelete();
            $table->foreignId('issue_status_id')->index()->constrained('issue_statuses')->cascadeOnDelete();
            $table->foreignUlid('feedback_status_id')->index()->constrained('feedback_statuses')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['board_id', 'issue_status_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_board_issue_status_maps');
    }
};
