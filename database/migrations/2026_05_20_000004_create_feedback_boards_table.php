<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_boards', static function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('service_product_id')->index()->constrained('service_products')->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('public_url');
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('allow_anonymous_read')->default(true);
            $table->string('default_sort', 24)->default('top');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['service_product_id', 'slug']);
            $table->index(['is_public', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_boards');
    }
};
