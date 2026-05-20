<?php

namespace App\Http\Controllers\Api\V1\Feedback;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Feedback\StoreCommentRequest;
use App\Http\Requests\Api\V1\Feedback\UpdateCommentRequest;
use App\Http\Resources\Api\V1\Feedback\FeedbackCommentResource;
use App\Models\FeedbackComment;
use App\Models\FeedbackIdentity;
use App\Models\FeedbackPost;
use App\Models\ServiceProduct;
use App\Services\Feedback\MarkdownRenderer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function __construct(private MarkdownRenderer $markdown) {}

    /**
     * @group Feedback
     */
    public function index(Request $request, FeedbackPost $post): AnonymousResourceCollection
    {
        $this->assertProductOwnsPost($request, $post);
        $identity = $request->attributes->get('feedback_identity');

        $comments = $post->comments()
            ->whereTopLevel()
            ->with([
                'identity',
                'staffUser',
                'votes' => fn ($q) => $identity instanceof FeedbackIdentity ? $q->where('identity_id', $identity->getKey()) : $q->whereRaw('1 = 0'),
                'replies.identity',
                'replies.staffUser',
                'replies.votes' => fn ($q) => $identity instanceof FeedbackIdentity ? $q->where('identity_id', $identity->getKey()) : $q->whereRaw('1 = 0'),
            ])
            ->oldest()
            ->cursorPaginate(min((int) $request->integer('per_page', 20), 50));

        return FeedbackCommentResource::collection($comments);
    }

    /**
     * @group Feedback
     */
    public function store(StoreCommentRequest $request, FeedbackPost $post): FeedbackCommentResource
    {
        $this->assertProductOwnsPost($request, $post);
        $identity = $this->identityForWrite($request);
        $data = $request->validated();

        $parent = null;
        if (! empty($data['parent_comment_id'])) {
            /** @var FeedbackComment $parent */
            $parent = FeedbackComment::query()
                ->where('post_id', $post->getKey())
                ->whereKey($data['parent_comment_id'])
                ->firstOrFail();

            if ($parent->parent_comment_id !== null) {
                abort(422, 'Replies can only be nested one level deep.');
            }
        }

        /** @var FeedbackComment $comment */
        $comment = DB::transaction(function () use ($post, $identity, $data): FeedbackComment {
            $comment = FeedbackComment::query()->create([
                'post_id' => $post->getKey(),
                'identity_id' => $identity->getKey(),
                'parent_comment_id' => $data['parent_comment_id'] ?? null,
                'body' => $data['body'],
                'body_html' => $this->markdown->render($data['body']),
                'is_staff_reply' => false,
            ]);

            $post->forceFill([
                'comment_count' => $post->comments()->count(),
                'last_activity_at' => now(),
            ])->save();

            return $comment;
        });

        return FeedbackCommentResource::make($comment->load(['identity', 'staffUser', 'votes', 'replies']));
    }

    /**
     * @group Feedback
     */
    public function update(UpdateCommentRequest $request, FeedbackComment $comment): FeedbackCommentResource
    {
        $this->assertProductOwnsPost($request, $comment->post);
        $identity = $this->identityForWrite($request);
        $this->assertAuthorWindow($comment, $identity);

        $comment->forceFill([
            'body' => $request->string('body')->toString(),
            'body_html' => $this->markdown->render($request->string('body')->toString()),
        ])->save();

        return FeedbackCommentResource::make($comment->fresh(['identity', 'staffUser', 'votes', 'replies']));
    }

    /**
     * @group Feedback
     */
    public function destroy(Request $request, FeedbackComment $comment): JsonResponse
    {
        $this->assertProductOwnsPost($request, $comment->post);
        $identity = $this->identityForWrite($request);
        $this->assertAuthorWindow($comment, $identity);

        DB::transaction(static function () use ($comment): void {
            $post = $comment->post;
            $comment->delete();
            $post?->forceFill(['comment_count' => $post->comments()->count()])->save();
        });

        return response()->json(['message' => 'Comment deleted.']);
    }

    private function identityForWrite(Request $request): FeedbackIdentity
    {
        /** @var FeedbackIdentity $identity */
        $identity = $request->attributes->get('feedback_identity');

        abort_if($identity->email_verified_at === null, 403, 'Email verification is required.');
        abort_if($identity->isBlocked(), 403, 'This feedback identity is blocked.');

        return $identity;
    }

    private function assertAuthorWindow(FeedbackComment $comment, FeedbackIdentity $identity): void
    {
        abort_if($comment->identity_id !== $identity->getKey(), 403);
        abort_if($comment->created_at?->lt(now()->subMinutes(5)), 403, 'The edit window has closed.');
    }

    private function assertProductOwnsPost(Request $request, ?FeedbackPost $post): void
    {
        /** @var ServiceProduct|null $product */
        $product = $request->attributes->get('service_product');

        abort_if($post === null || $post->board()->value('service_product_id') !== $product?->getKey(), 404);
    }
}
