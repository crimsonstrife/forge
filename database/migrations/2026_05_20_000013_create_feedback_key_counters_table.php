<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_key_counters', static function (Blueprint $table): void {
            $table->foreignUlid('board_id')->primary()->constrained('feedback_boards')->cascadeOnDelete();
            $table->unsignedBigInteger('current_value')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_key_counters');
    }
};
