<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('service_product_type_maps', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('service_product_id')->index();
            $table->foreignId('ticket_type_id')->index(); // -> ticket_types.id
            $table->foreignId('issue_type_id')->index();  // -> issue_types.id
            $table->timestamps();
            $table->unique(['service_product_id', 'ticket_type_id'], 'sp_type_uq');
        });

        Schema::create('service_product_status_maps', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('service_product_id')->index();
            $table->foreignId('ticket_status_id')->index(); // -> ticket_statuses.id
            $table->foreignId('issue_status_id')->index();  // -> issue_statuses.id
            $table->timestamps();
            $table->unique(['service_product_id', 'ticket_status_id'], 'sp_status_uq');
        });

        Schema::create('service_product_priority_maps', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('service_product_id')->index();
            $table->foreignId('ticket_priority_id')->index(); // -> ticket_priorities.id
            $table->foreignId('issue_priority_id')->index();  // -> issue_priorities.id
            $table->timestamps();
            $table->unique(['service_product_id', 'ticket_priority_id'], 'sp_priority_uq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_product_priority_maps');
        Schema::dropIfExists('service_product_status_maps');
        Schema::dropIfExists('service_product_type_maps');
    }
};
