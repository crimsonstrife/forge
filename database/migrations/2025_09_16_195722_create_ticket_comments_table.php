<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_comments', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('ticket_id')->index();
            $table->foreignUuid('user_id')->nullable()->index(); // null = public/customer
            $table->text('body');
            $table->text('redacted_body')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_comments');
    }
};
