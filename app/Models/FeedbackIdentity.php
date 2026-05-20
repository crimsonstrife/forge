<?php

namespace App\Models;

use App\Support\ActivityContext;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FeedbackIdentity extends Authenticatable
{
    use HasFactory;
    use HasUlids;
    use LogsActivity;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'email_encrypted' => 'encrypted',
            'email_verified_at' => 'immutable_datetime',
            'blocked_at' => 'immutable_datetime',
            'last_seen_at' => 'immutable_datetime',
        ];
    }

    public static function emailHash(string $email): string
    {
        return hash('sha256', Str::lower(trim($email)));
    }

    public function isBlocked(): bool
    {
        return $this->blocked_at !== null;
    }

    /** @return HasMany<FeedbackPost> */
    public function posts(): HasMany
    {
        return $this->hasMany(FeedbackPost::class, 'identity_id');
    }

    /** @return HasMany<FeedbackComment> */
    public function comments(): HasMany
    {
        return $this->hasMany(FeedbackComment::class, 'identity_id');
    }

    /** @return HasMany<FeedbackPostVote> */
    public function postVotes(): HasMany
    {
        return $this->hasMany(FeedbackPostVote::class, 'identity_id');
    }

    /** @return HasMany<FeedbackCommentVote> */
    public function commentVotes(): HasMany
    {
        return $this->hasMany(FeedbackCommentVote::class, 'identity_id');
    }

    /** @return HasMany<FeedbackMagicLink> */
    public function magicLinks(): HasMany
    {
        return $this->hasMany(FeedbackMagicLink::class, 'identity_id');
    }

    /** @return HasMany<FeedbackSession> */
    public function sessions(): HasMany
    {
        return $this->hasMany(FeedbackSession::class, 'identity_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('forge.feedback.identity')
            ->logOnly(['display_name', 'avatar_url', 'blocked_at', 'blocked_reason', 'blocked_by_user_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity): void
    {
        $ctx = ActivityContext::base();
        $activity->team_id = $ctx['team_id'];
        $activity->properties = $activity->properties->merge([
            'actor_id' => $ctx['user_id'],
            'ip' => $ctx['ip'],
            'ua' => $ctx['user_agent'],
            'identity_id' => $this->getKey(),
        ]);
        $activity->description = 'feedback.identity.'.($activity->event ?? 'updated');
    }
}
