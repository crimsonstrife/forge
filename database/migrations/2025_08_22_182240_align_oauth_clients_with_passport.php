<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            // Passport's ClientRepository writes these columns directly
            $table->string('user_id', 36)->nullable()->index()->after('id');
            $table->text('redirect')->nullable()->after('provider');
            $table->boolean('personal_access_client')->default(false)->after('redirect');
            $table->boolean('password_client')->default(false)->after('personal_access_client');
        });
    }

    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'redirect', 'personal_access_client', 'password_client']);
        });
    }

    public function getConnection(): ?string
    {
        return $this->connection ?? config('passport.connection');
    }
};
