<?php

namespace App\Http\Controllers\Api\V1\Feedback\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Feedback\AdminConvertIssueRequest;
use App\Http\Requests\Api\V1\Feedback\AdminMergeRequest;
use App\Http\Requests\Api\V1\Feedback\AdminPinRequest;
use App\Http\Requests\Api\V1\Feedback\AdminPostUpdateRequest;
use App\Http\Requests\Api\V1\Feedback\AdminStatusRequest;
use App\Http\Requests\Api\V1\Feedback\StoreCommentRequest;
use App\Http\Resources\Api\V1\Feedback\FeedbackCommentResource;
use App\Http\Resources\Api\V1\Feedback\FeedbackPostResource;
use App\Http\Resources\Api\V1\IssueResource;
use App\Models\FeedbackBoard;
use App\Models\FeedbackComment;
use App\Models\FeedbackPost;
use App\Models\Issue;
use App\Models\IssueExternalRef;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Project;
use App\Services\Feedback\MarkdownRenderer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminPostController extends Controller
{
    public function __construct(private MarkdownRenderer $markdown) {}

    /**
     * @group Feedback
     */
    public function index(FeedbackBoard $board): AnonymousResourceCollection
    {
        return FeedbackPostResource::collection(
            $board->posts()
                ->with(['board:id,public_url', 'identity', 'status', 'category', 'votes' => fn ($q) => $q->whereRaw('1 = 0')])
                ->latest('last_activity_at')
                ->paginate(25)
        );
    }

    /**
     * @group Feedback
     */
    public function update(AdminPostUpdateRequest $request, FeedbackPost $post): FeedbackPostResource
    {
        $data = $request->validated();
        if (array_key_exists('body', $data)) {
            $data['body_html'] = $this->markdown->render($data['body']);
        }

        $post->fill($data)->save();

        return FeedbackPostResource::make($post->fresh($this->includes()));
    }

    /**
     * @group Feedback
     */
    public function status(AdminStatusRequest $request, FeedbackPost $post): FeedbackPostResource
    {
        $status = $post->board->statuses()->where('slug', $request->string('status_slug')->toString())->firstOrFail();

        $post->forceFill([
            'status_id' => $status->getKey(),
            'last_activity_at' => now(),
        ])->save();

        return FeedbackPostResource::make($post->fresh($this->includes()));
    }

    /**
     * @group Feedback
     */
    public function pin(AdminPinRequest $request, FeedbackPost $post): FeedbackPostResource
    {
        $pinned = $request->boolean('pinned');

        $post->forceFill([
            'is_pinned' => $pinned,
            'pinned_at' => $pinned ? now() : null,
        ])->save();

        return FeedbackPostResource::make($post->fresh($this->includes()));
    }

    /**
     * @group Feedback
     */
    public function merge(AdminMergeRequest $request, FeedbackPost $post): FeedbackPostResource
    {
        $target = FeedbackPost::query()
            ->where('board_id', $post->board_id)
            ->whereKey($request->string('target_post_id')->toString())
            ->firstOrFail();

        abort_if($target->is($post), 422, 'A post cannot be merged into itself.');

        DB::transaction(function () use ($request, $post, $target): void {
            $duplicate = $post->board->statuses()->where('slug', 'duplicate')->first();

            $post->forceFill([
                'merged_into_post_id' => $target->getKey(),
                'status_id' => $duplicate?->getKey() ?? $post->status_id,
            ])->save();

            foreach ($post->votes as $vote) {
                $target->votes()->firstOrCreate(
                    ['identity_id' => $vote->identity_id],
                    ['value' => $vote->value]
                );
            }

            FeedbackComment::query()->create([
                'post_id' => $target->getKey(),
                'staff_user_id' => $request->user()?->getKey(),
                'body' => 'Merged from '.$post->key.' by '.($request->user()?->name ?? 'staff').'.',
                'body_html' => '<p>Merged from '.$post->key.' by '.e($request->user()?->name ?? 'staff').'.</p>',
                'is_staff_reply' => true,
            ]);

            $target->recountVotes();
            $target->forceFill([
                'comment_count' => $target->comments()->count(),
                'last_activity_at' => now(),
            ])->save();
        });

        return FeedbackPostResource::make($post->fresh($this->includes()));
    }

    /**
     * @group Feedback
     */
    public function convertIssue(AdminConvertIssueRequest $request, FeedbackPost $post): IssueResource
    {
        $data = $request->validated();
        /** @var Project $project */
        $project = Project::query()->findOrFail($data['project_id']);

        /** @var Issue $issue */
        $issue = DB::transaction(function () use ($request, $post, $project, $data): Issue {
            $issue = Issue::query()->create([
                'project_id' => $project->getKey(),
                'summary' => $post->title,
                'description' => $post->body."\n\n---\n\nSource: feedback post {$post->key}\n".rtrim((string) $post->board->public_url, '/').'/posts/'.$post->key,
                'reporter_id' => $request->user()?->getKey(),
                'issue_status_id' => $project->initialStatusId() ?? IssueStatus::query()->where('is_done', false)->orderBy('order')->value('id'),
                'issue_type_id' => $data['issue_type_id'] ?? $project->defaultTypeId() ?? IssueType::query()->where('is_default', true)->value('id'),
                'issue_priority_id' => $data['priority_id'] ?? $this->defaultPriorityId($project),
            ]);

            DB::table('feedback_post_issue_links')->insertOrIgnore([
                [
                    'id' => (string) Str::ulid(),
                    'post_id' => $post->getKey(),
                    'issue_id' => $issue->getKey(),
                    'created_by_user_id' => $request->user()?->getKey(),
                    'created_at' => now(),
                ],
            ]);

            IssueExternalRef::query()->firstOrCreate(
                ['source_type' => 'feedback_post', 'source_id' => $post->getKey()],
                [
                    'issue_id' => $issue->getKey(),
                    'provider' => 'feedback',
                    'external_issue_id' => $post->getKey(),
                    'url' => rtrim((string) $post->board->public_url, '/').'/posts/'.$post->key,
                    'state' => 'linked',
                    'payload' => ['post_key' => $post->key],
                ]
            );

            if ($request->boolean('mark_planned')) {
                $planned = $post->board->statuses()->where('slug', 'planned')->first();
                if ($planned !== null) {
                    $post->forceFill([
                        'status_id' => $planned->getKey(),
                        'last_activity_at' => now(),
                    ])->save();
                }
            }

            activity('forge.feedback.post')
                ->performedOn($post)
                ->causedBy($request->user())
                ->withProperties(['issue_id' => $issue->getKey()])
                ->log('feedback.post.converted_to_issue');

            activity('forge.issue')
                ->performedOn($issue)
                ->causedBy($request->user())
                ->withProperties(['feedback_post_id' => $post->getKey()])
                ->log('issue.created_from_feedback_post');

            return $issue;
        });

        return IssueResource::make($issue->fresh(['status', 'priority', 'type', 'project']));
    }

    /**
     * @group Feedback
     */
    public function comments(StoreCommentRequest $request, FeedbackPost $post): FeedbackCommentResource
    {
        /** @var FeedbackComment $comment */
        $comment = DB::transaction(function () use ($request, $post): FeedbackComment {
            $comment = FeedbackComment::query()->create([
                'post_id' => $post->getKey(),
                'staff_user_id' => $request->user()?->getKey(),
                'parent_comment_id' => $request->input('parent_comment_id'),
                'body' => $request->string('body')->toString(),
                'body_html' => $this->markdown->render($request->string('body')->toString()),
                'is_staff_reply' => true,
            ]);

            $post->forceFill([
                'comment_count' => $post->comments()->count(),
                'last_activity_at' => now(),
            ])->save();

            return $comment;
        });

        return FeedbackCommentResource::make($comment->load(['identity', 'staffUser', 'votes', 'replies']));
    }

    /** @return array<string,mixed> */
    private function includes(): array
    {
        return [
            'board:id,public_url',
            'identity',
            'status',
            'category',
            'votes' => fn ($q) => $q->whereRaw('1 = 0'),
            'issues:id,key',
        ];
    }

    private function defaultPriorityId(Project $project): ?int
    {
        return $project->issuePriorities()->wherePivot('is_default', true)->value('issue_priorities.id')
            ?? IssuePriority::query()->where('weight', 50)->value('id')
            ?? IssuePriority::query()->orderBy('weight')->value('id');
    }
}
