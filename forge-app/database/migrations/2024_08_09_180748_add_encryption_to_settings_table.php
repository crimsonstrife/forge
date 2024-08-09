<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('is_encrypted')->default(false);
            $table->string('encryption_key')->nullable();
            $table->enum('encryption_type', ['crypt', 'bcrypt', 'argon', 'argon2id'])->nullable()->default('crypt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('is_encrypted');
            $table->dropColumn('encryption_key');
            $table->dropColumn('encryption_type');
        });
    }
};
