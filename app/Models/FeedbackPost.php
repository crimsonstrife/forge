<?php

namespace App\Models;

use App\Services\Feedback\FeedbackKeyService;
use App\Support\ActivityContext;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FeedbackPost extends Model
{
    use HasFactory;
    use HasUlids;
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'pinned_at' => 'immutable_datetime',
            'last_activity_at' => 'immutable_datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(static function (self $post): void {
            if (empty($post->key) && $post->board_id) {
                $board = $post->relationLoaded('board') ? $post->board : FeedbackBoard::query()->findOrFail($post->board_id);
                $post->key = app(FeedbackKeyService::class)->nextKey($board);
            }

            if (empty($post->last_activity_at)) {
                $post->last_activity_at = now();
            }
        });
    }

    /** @return BelongsTo<FeedbackBoard,FeedbackPost> */
    public function board(): BelongsTo
    {
        return $this->belongsTo(FeedbackBoard::class, 'board_id');
    }

    /** @return BelongsTo<FeedbackIdentity,FeedbackPost> */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(FeedbackIdentity::class, 'identity_id');
    }

    /** @return BelongsTo<FeedbackStatus,FeedbackPost> */
    public function status(): BelongsTo
    {
        return $this->belongsTo(FeedbackStatus::class, 'status_id');
    }

    /** @return BelongsTo<FeedbackCategory,FeedbackPost> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FeedbackCategory::class, 'category_id');
    }

    /** @return BelongsTo<FeedbackPost,FeedbackPost> */
    public function mergedIntoPost(): BelongsTo
    {
        return $this->belongsTo(self::class, 'merged_into_post_id');
    }

    /** @return HasMany<FeedbackPostVote> */
    public function votes(): HasMany
    {
        return $this->hasMany(FeedbackPostVote::class, 'post_id');
    }

    /** @return HasMany<FeedbackComment> */
    public function comments(): HasMany
    {
        return $this->hasMany(FeedbackComment::class, 'post_id');
    }

    /** @return BelongsToMany<Issue> */
    public function issues(): BelongsToMany
    {
        return $this->belongsToMany(Issue::class, 'feedback_post_issue_links', 'post_id', 'issue_id')
            ->withPivot(['created_by_user_id', 'created_at']);
    }

    public function recountVotes(): void
    {
        $up = (int) $this->votes()->where('value', 1)->count();
        $down = (int) $this->votes()->where('value', -1)->count();

        $this->forceFill([
            'upvote_count' => $up,
            'downvote_count' => $down,
            'net_score' => $up - $down,
        ])->save();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('forge.feedback.post')
            ->logOnly(['key', 'title', 'body', 'status_id', 'category_id', 'is_pinned', 'merged_into_post_id'])
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
            'post_id' => $this->getKey(),
        ]);
        $activity->description = 'feedback.post.'.($activity->event ?? 'updated');
    }
}
