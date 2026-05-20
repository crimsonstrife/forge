<?php

namespace App\Http\Resources\Api\V1\Feedback;

use App\Models\FeedbackComment;
use App\Models\FeedbackIdentity;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property FeedbackComment $resource */
class FeedbackCommentResource extends JsonResource
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
            'body' => $this->body,
            'body_html' => $this->body_html,
            'parent_comment_id' => $this->parent_comment_id,
            'is_staff_reply' => (bool) $this->is_staff_reply,
            'author' => $this->is_staff_reply ? [
                'display_name' => trim(($this->staffUser?->name ?? __('Staff')).' (Staff)'),
                'avatar_url' => $this->staffUser?->profile_photo_url,
                'is_staff' => true,
            ] : ($this->identity ? FeedbackIdentityResource::make($this->identity)->resolve($request) : null),
            'net_score' => (int) $this->net_score,
            'my_vote' => $vote,
            'replies' => $this->whenLoaded('replies', fn () => self::collection($this->replies)->resolve($request)),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
