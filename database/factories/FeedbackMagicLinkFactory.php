<?php

namespace Database\Factories;

use App\Models\FeedbackIdentity;
use App\Models\FeedbackMagicLink;
use App\Models\ServiceProduct;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FeedbackMagicLinkFactory extends Factory
{
    protected $model = FeedbackMagicLink::class;

    public function definition(): array
    {
        $identity = FeedbackIdentity::factory()->create();

        return [
            'email_hash' => $identity->email_hash,
            'identity_id' => $identity->getKey(),
            'service_product_id' => ServiceProduct::factory(),
            'token_hash' => hash('sha256', Str::random(40)),
            'expires_at' => now()->addMinutes(15),
            'request_ip' => fake()->ipv4(),
            'user_agent' => 'PHPUnit',
        ];
    }
}
