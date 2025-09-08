<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->foreignUuid('repository_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type')->nullable();
            $table->string('signature')->nullable();
            $table->json('headers')->nullable();
            $table->longText('payload');       // raw body
            $table->integer('http_status')->nullable();
            $table->text('processing_error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
    }
};
