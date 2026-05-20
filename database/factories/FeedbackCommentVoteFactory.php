<?php

namespace Database\Factories;

use App\Models\FeedbackComment;
use App\Models\FeedbackCommentVote;
use App\Models\FeedbackIdentity;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackCommentVoteFactory extends Factory
{
    protected $model = FeedbackCommentVote::class;

    public function definition(): array
    {
        return [
            'comment_id' => FeedbackComment::factory(),
            'identity_id' => FeedbackIdentity::factory(),
            'value' => 1,
        ];
    }
}
