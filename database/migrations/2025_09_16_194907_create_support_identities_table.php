<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('support_identities', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('email_encrypted'); // cast=encrypted on model
            $table->char('email_hash', 64)->unique()->index();
            $table->ulid('token')->unique()->index();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_identities');
    }
};
