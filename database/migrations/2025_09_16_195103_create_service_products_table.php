<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_products', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUuid('organization_id')->index();
            $table->string('key', 32)->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignUuid('default_project_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'key']);
        });

        Schema::create('project_service_product', static function (Blueprint $table): void {
            $table->foreignUuid('project_id')->index();
            $table->foreignUlid('service_product_id')->index();
            $table->primary(['project_id', 'service_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_service_product');
        Schema::dropIfExists('service_products');
    }
};
