<?php

namespace App\Http\Resources\Api\V1\Feedback;

use App\Models\FeedbackIdentity;
use App\Models\FeedbackPost;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @property FeedbackPost $resource */
class FeedbackPostResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray($request): array
    {
        $identity = $request->attributes->get('feedback_identity');
        $vote = $identity instanceof FeedbackIdentity
            ? $this->votes->firstWhere('identity_id', $identity->getKey())?->value
            : null;

        return [
            'id' => (string) $this->id,
            'key' => $this->key,
            'title' => $this->title,
            'body_excerpt' => Str::limit(trim(strip_tags((string) $this->body_html)), 200),
            'body' => $this->when($this->resource->relationLoaded('comments') || $request->routeIs('api.v1.feedback.posts.show'), $this->body),
            'body_html' => $this->when($this->resource->relationLoaded('comments') || $request->routeIs('api.v1.feedback.posts.show'), $this->body_html),
            'status' => [
                'id' => (string) $this->status?->id,
                'slug' => $this->status?->slug,
                'name' => $this->status?->name,
                'color' => $this->status?->color,
            ],
            'category' => $this->category ? [
                'id' => (string) $this->category->id,
                'slug' => $this->category->slug,
                'name' => $this->category->name,
                'color' => $this->category->color,
            ] : null,
            'author' => $this->identity ? FeedbackIdentityResource::make($this->identity)->resolve($request) : null,
            'upvote_count' => $this->when($request->routeIs('api.v1.feedback.posts.show'), (int) $this->upvote_count),
            'downvote_count' => $this->when($request->routeIs('api.v1.feedback.posts.show'), (int) $this->downvote_count),
            'net_score' => (int) $this->net_score,
            'comment_count' => (int) $this->comment_count,
            'is_pinned' => (bool) $this->is_pinned,
            'my_vote' => $vote,
            'merged_into_post_id' => $this->when($request->routeIs('api.v1.feedback.posts.show'), $this->merged_into_post_id),
            'linked_issue_keys' => $this->whenLoaded('issues', fn () => $this->issues->pluck('key')->values()),
            'comments' => $this->whenLoaded('comments', fn () => FeedbackCommentResource::collection($this->comments)->resolve($request)),
            'created_at' => $this->created_at?->toIso8601String(),
            'last_activity_at' => $this->last_activity_at?->toIso8601String(),
            'url' => rtrim((string) $this->board?->public_url, '/').'/posts/'.$this->key,
        ];
    }
}
