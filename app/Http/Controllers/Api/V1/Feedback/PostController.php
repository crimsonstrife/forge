<?php

namespace App\Http\Controllers\Api\V1\Feedback;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Feedback\StorePostRequest;
use App\Http\Requests\Api\V1\Feedback\UpdatePostRequest;
use App\Http\Resources\Api\V1\Feedback\FeedbackPostResource;
use App\Models\FeedbackIdentity;
use App\Models\FeedbackPost;
use App\Models\ServiceProduct;
use App\Services\Feedback\FeedbackBoardResolver;
use App\Services\Feedback\MarkdownRenderer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    public function __construct(
        private MarkdownRenderer $markdown,
        private FeedbackBoardResolver $boards,
    ) {}

    /**
     * @group Feedback
     */
    public function index(Request $request, string $slug): AnonymousResourceCollection
    {
        $board = $this->boards->forRequest($request, $slug);
        $identity = $request->attributes->get('feedback_identity');

        if (! $board->allow_anonymous_read && ! $identity instanceof FeedbackIdentity) {
            abort(401, 'A valid feedback session is required.');
        }

        $posts = FeedbackPost::query()
            ->where('board_id', $board->getKey())
            ->whereNull('merged_into_post_id')
            ->with($this->postIncludes($identity))
            ->when($request->string('status')->toString(), fn ($q, $status) => $q->whereHas('status', fn ($s) => $s->where('slug', $status)))
            ->when($request->string('category')->toString(), fn ($q, $category) => $q->whereHas('category', fn ($c) => $c->where('slug', $category)))
            ->when($request->string('q')->toString(), fn ($q, $search) => $q->where('title', 'like', '%'.$search.'%'))
            ->when($request->string('sort', $board->default_sort)->toString() === 'new', fn ($q) => $q->latest())
            ->when($request->string('sort', $board->default_sort)->toString() === 'trending', fn ($q) => $q->orderByDesc('trending_score'))
            ->when(! in_array($request->string('sort', $board->default_sort)->toString(), ['new', 'trending'], true), fn ($q) => $q->orderByDesc('is_pinned')->orderByDesc('net_score')->latest('last_activity_at'))
            ->cursorPaginate(min((int) $request->integer('per_page', 20), 50));

        return FeedbackPostResource::collection($posts);
    }

    /**
     * @group Feedback
     */
    public function show(Request $request, FeedbackPost $post): FeedbackPostResource
    {
        $this->assertProductOwnsPost($request, $post);
        $identity = $request->attributes->get('feedback_identity');

        return FeedbackPostResource::make($post->load([
            ...$this->postIncludes($identity),
            'issues:id,key',
            'comments' => fn ($q) => $q->whereTopLevel()->with(['identity', 'staffUser', 'votes' => fn ($v) => $identity instanceof FeedbackIdentity ? $v->where('identity_id', $identity->getKey()) : $v->whereRaw('1 = 0'), 'replies.identity', 'replies.staffUser', 'replies.votes'])->oldest()->limit(20),
        ]));
    }

    /**
     * @group Feedback
     */
    public function store(StorePostRequest $request, string $slug): FeedbackPostResource
    {
        $board = $this->boards->forRequest($request, $slug);
        $identity = $this->identityForWrite($request);
        $data = $request->validated();

        if (! empty($data['category_id']) && ! $board->categories()->whereKey($data['category_id'])->exists()) {
            abort(422, 'The selected category does not belong to this board.');
        }

        $status = $board->defaultStatus() ?? $board->statuses()->firstOrFail();

        /** @var FeedbackPost $post */
        $post = FeedbackPost::query()->create([
            'board_id' => $board->getKey(),
            'identity_id' => $identity->getKey(),
            'status_id' => $status->getKey(),
            'category_id' => $data['category_id'] ?? null,
            'title' => $data['title'],
            'body' => $data['body'],
            'body_html' => $this->markdown->render($data['body']),
            'last_activity_at' => now(),
        ]);

        return FeedbackPostResource::make($post->load($this->postIncludes($identity)));
    }

    /**
     * @group Feedback
     */
    public function update(UpdatePostRequest $request, FeedbackPost $post): FeedbackPostResource
    {
        $this->assertProductOwnsPost($request, $post);
        $identity = $this->identityForWrite($request);
        $this->assertAuthorWindow($post, $identity);

        $data = $request->validated();
        if (array_key_exists('body', $data)) {
            $data['body_html'] = $this->markdown->render($data['body']);
        }

        $post->fill($data)->save();

        return FeedbackPostResource::make($post->fresh($this->postIncludes($identity)));
    }

    /**
     * @group Feedback
     */
    public function destroy(Request $request, FeedbackPost $post): JsonResponse
    {
        $this->assertProductOwnsPost($request, $post);
        $identity = $this->identityForWrite($request);
        $this->assertAuthorWindow($post, $identity);

        $post->delete();

        return response()->json(['message' => 'Post deleted.']);
    }

    /** @return array<string,mixed> */
    private function postIncludes(mixed $identity): array
    {
        return [
            'board:id,public_url',
            'identity',
            'status',
            'category',
            'votes' => fn ($q) => $identity instanceof FeedbackIdentity ? $q->where('identity_id', $identity->getKey()) : $q->whereRaw('1 = 0'),
        ];
    }

    private function identityForWrite(Request $request): FeedbackIdentity
    {
        /** @var FeedbackIdentity $identity */
        $identity = $request->attributes->get('feedback_identity');

        abort_if($identity->email_verified_at === null, 403, 'Email verification is required.');
        abort_if($identity->isBlocked(), 403, 'This feedback identity is blocked.');

        return $identity;
    }

    private function assertAuthorWindow(FeedbackPost $post, FeedbackIdentity $identity): void
    {
        abort_if($post->identity_id !== $identity->getKey(), 403);
        abort_if($post->created_at?->lt(now()->subMinutes(5)), 403, 'The edit window has closed.');
    }

    private function assertProductOwnsPost(Request $request, FeedbackPost $post): void
    {
        /** @var ServiceProduct|null $product */
        $product = $request->attributes->get('service_product');
        $boardProductId = $post->board()->value('service_product_id');

        abort_if($boardProductId !== $product?->getKey(), 404);
    }
}
