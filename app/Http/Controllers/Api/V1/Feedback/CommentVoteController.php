<?php

namespace App\Http\Controllers\Api\V1\Feedback;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Feedback\VoteRequest;
use App\Http\Resources\Api\V1\Feedback\FeedbackCommentResource;
use App\Models\FeedbackComment;
use App\Models\FeedbackCommentVote;
use App\Models\FeedbackIdentity;
use App\Models\ServiceProduct;
use Illuminate\Support\Facades\DB;

class CommentVoteController extends Controller
{
    /**
     * @group Feedback
     */
    public function store(VoteRequest $request, FeedbackComment $comment): FeedbackCommentResource
    {
        /** @var ServiceProduct|null $product */
        $product = $request->attributes->get('service_product');
        abort_if($comment->post?->board()->value('service_product_id') !== $product?->getKey(), 404);

        /** @var FeedbackIdentity $identity */
        $identity = $request->attributes->get('feedback_identity');
        abort_if($identity->email_verified_at === null, 403, 'Email verification is required.');
        abort_if($identity->isBlocked(), 403, 'This feedback identity is blocked.');

        DB::transaction(static function () use ($request, $comment, $identity): void {
            $value = (int) $request->integer('value');

            if ($value === 0) {
                FeedbackCommentVote::query()
                    ->where('comment_id', $comment->getKey())
                    ->where('identity_id', $identity->getKey())
                    ->delete();
            } else {
                FeedbackCommentVote::query()->updateOrCreate(
                    ['comment_id' => $comment->getKey(), 'identity_id' => $identity->getKey()],
                    ['value' => $value]
                );
            }

            $comment->recountVotes();
        });

        return FeedbackCommentResource::make($comment->fresh([
            'identity',
            'staffUser',
            'votes' => fn ($q) => $q->where('identity_id', $identity->getKey()),
            'replies',
        ]));
    }
}
