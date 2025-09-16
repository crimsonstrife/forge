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
        Schema::create('issue_link_types', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique();                  // e.g. blocks, clones, relates-to
            $table->string('name');                           // outward label: "blocks"
            $table->string('inverse_name')->nullable();       // inward label: "is blocked by"
            $table->boolean('is_symmetric')->default(false);  // true for "relates to"
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);     // lock defaults
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_link_types');
    }
};
