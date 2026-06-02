<?php

namespace App\Http\Controllers\Api\V1\Feedback;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Feedback\VoteRequest;
use App\Http\Resources\Api\V1\Feedback\FeedbackPostResource;
use App\Models\FeedbackIdentity;
use App\Models\FeedbackPost;
use App\Models\FeedbackPostVote;
use App\Models\ServiceProduct;
use Illuminate\Support\Facades\DB;

class PostVoteController extends Controller
{
    /**
     * @group Feedback
     */
    public function store(VoteRequest $request, FeedbackPost $post): FeedbackPostResource
    {
        /** @var ServiceProduct|null $product */
        $product = $request->attributes->get('service_product');
        abort_if($post->board()->value('service_product_id') !== $product?->getKey(), 404);

        /** @var FeedbackIdentity $identity */
        $identity = $request->attributes->get('feedback_identity');
        abort_if($identity->email_verified_at === null, 403, 'Email verification is required.');
        abort_if($identity->isBlocked(), 403, 'This feedback identity is blocked.');

        DB::transaction(static function () use ($request, $post, $identity): void {
            $value = (int) $request->integer('value');

            if ($value === 0) {
                FeedbackPostVote::query()
                    ->where('post_id', $post->getKey())
                    ->where('identity_id', $identity->getKey())
                    ->delete();
            } else {
                FeedbackPostVote::query()->updateOrCreate(
                    ['post_id' => $post->getKey(), 'identity_id' => $identity->getKey()],
                    ['value' => $value]
                );
            }

            $post->recountVotes();
        });

        return FeedbackPostResource::make($post->fresh([
            'board:id,public_url',
            'identity',
            'status',
            'category',
            'votes' => fn ($q) => $q->where('identity_id', $identity->getKey()),
        ]));
    }
}
