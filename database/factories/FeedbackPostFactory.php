<?php

namespace Database\Factories;

use App\Models\FeedbackBoard;
use App\Models\FeedbackIdentity;
use App\Models\FeedbackPost;
use App\Models\FeedbackStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackPostFactory extends Factory
{
    protected $model = FeedbackPost::class;

    public function definition(): array
    {
        $board = FeedbackBoard::factory()->create();
        $status = $board->defaultStatus() ?? FeedbackStatus::factory()->create(['board_id' => $board->getKey(), 'is_default' => true]);
        $body = fake()->paragraph();

        return [
            'board_id' => $board->getKey(),
            'identity_id' => FeedbackIdentity::factory(),
            'status_id' => $status->getKey(),
            'category_id' => null,
            'title' => fake()->sentence(6),
            'body' => $body,
            'body_html' => '<p>'.e($body).'</p>',
            'last_activity_at' => now(),
        ];
    }
}
