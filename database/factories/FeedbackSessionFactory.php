<?php

namespace Database\Factories;

use App\Models\FeedbackIdentity;
use App\Models\FeedbackSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FeedbackSessionFactory extends Factory
{
    protected $model = FeedbackSession::class;

    public function definition(): array
    {
        return [
            'identity_id' => FeedbackIdentity::factory(),
            'token_hash' => hash('sha256', 'fb_sess_'.Str::random(43)),
            'expires_at' => now()->addDays(30),
            'last_seen_at' => now(),
            'last_seen_ip' => fake()->ipv4(),
            'user_agent' => 'PHPUnit',
        ];
    }
}
