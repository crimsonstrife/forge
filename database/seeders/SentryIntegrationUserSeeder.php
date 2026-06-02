<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SentryIntegrationUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) config('sentry-integration.system_user_email');
        $name = (string) config('sentry-integration.system_user_name');

        User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(Str::random(64)),
                'email_verified_at' => now(),
            ],
        );
    }
}
