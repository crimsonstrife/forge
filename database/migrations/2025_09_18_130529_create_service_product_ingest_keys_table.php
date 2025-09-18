<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('service_product_ingest_keys', static function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('service_product_id');
            $table->string('name', 100)->nullable(); // label to identify this key
            $table->string('secret_hash', 64);       // hex sha256 HMAC
            $table->uuid('created_by')->nullable();
            $table->timestampTz('last_used_at')->nullable();
            $table->timestampTz('revoked_at')->nullable();
            $table->timestampsTz();

            $table->foreign('service_product_id')->references('id')->on('service_products')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['service_product_id', 'revoked_at']);
            $table->index('last_used_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_product_ingest_keys');
    }
};
