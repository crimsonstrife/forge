<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_magic_links', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->char('email_hash', 64)->index();
            $table->foreignUlid('identity_id')->nullable()->index()->constrained('feedback_identities')->nullOnDelete();
            $table->foreignUlid('service_product_id')->index()->constrained('service_products')->cascadeOnDelete();
            $table->char('token_hash', 64)->unique();
            $table->timestamp('expires_at')->index();
            $table->timestamp('consumed_at')->nullable()->index();
            $table->string('request_ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_magic_links');
    }
};
