<?php

namespace Database\Factories;

use App\Models\FeedbackIdentity;
use App\Models\FeedbackPost;
use App\Models\FeedbackPostVote;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackPostVoteFactory extends Factory
{
    protected $model = FeedbackPostVote::class;

    public function definition(): array
    {
        return [
            'post_id' => FeedbackPost::factory(),
            'identity_id' => FeedbackIdentity::factory(),
            'value' => 1,
        ];
    }
}
