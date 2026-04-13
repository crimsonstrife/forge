<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_key_sequences', static function (Blueprint $table): void {
            $table->string('prefix', 32)->primary();
            $table->unsignedBigInteger('current_value')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_key_sequences');
    }
};
