<?php

namespace Database\Factories;

use App\Models\FeedbackIdentity;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackIdentityFactory extends Factory
{
    protected $model = FeedbackIdentity::class;

    public function definition(): array
    {
        $email = fake()->unique()->safeEmail();

        return [
            'email_encrypted' => $email,
            'email_hash' => FeedbackIdentity::emailHash($email),
            'display_name' => fake()->name(),
            'avatar_url' => null,
            'email_verified_at' => now(),
            'blocked_at' => null,
            'blocked_reason' => null,
            'last_seen_at' => now(),
            'last_seen_ip' => fake()->ipv4(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }

    public function blocked(): static
    {
        return $this->state(fn () => ['blocked_at' => now(), 'blocked_reason' => 'Spam']);
    }
}
