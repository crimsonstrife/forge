<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_identities', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->text('email_encrypted');
            $table->char('email_hash', 64)->unique()->index();
            $table->string('display_name')->nullable();
            $table->string('avatar_url')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->string('blocked_reason')->nullable();
            $table->foreignUuid('blocked_by_user_id')->nullable()->index()->constrained('users')->restrictOnDelete();
            $table->timestamp('last_seen_at')->nullable();
            $table->string('last_seen_ip')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_identities');
    }
};
