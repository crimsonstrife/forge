<?php

namespace App\Http\Resources\Api\V1\Feedback;

use App\Models\FeedbackIdentity;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @property FeedbackIdentity $resource */
class FeedbackIdentityResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray($request): array
    {
        $hash = $this->email_hash ?: md5((string) $this->id);

        return [
            'id' => (string) $this->id,
            'display_name' => $this->display_name,
            'avatar_url' => $this->avatar_url ?: 'https://www.gravatar.com/avatar/'.md5(Str::lower((string) $hash)).'?d=identicon',
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'blocked_at' => $this->blocked_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
