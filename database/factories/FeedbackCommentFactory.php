<?php

namespace Database\Factories;

use App\Models\FeedbackComment;
use App\Models\FeedbackIdentity;
use App\Models\FeedbackPost;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackCommentFactory extends Factory
{
    protected $model = FeedbackComment::class;

    public function definition(): array
    {
        $body = fake()->paragraph();

        return [
            'post_id' => FeedbackPost::factory(),
            'identity_id' => FeedbackIdentity::factory(),
            'staff_user_id' => null,
            'parent_comment_id' => null,
            'body' => $body,
            'body_html' => '<p>'.e($body).'</p>',
            'is_staff_reply' => false,
        ];
    }
}
