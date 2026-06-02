<?php

namespace App\Models;

use App\Support\ActivityContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FeedbackComment extends Model
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
            'is_staff_reply' => 'boolean',
        ];
    }

    /** @return BelongsTo<FeedbackPost,FeedbackComment> */
    public function post(): BelongsTo
    {
        return $this->belongsTo(FeedbackPost::class, 'post_id');
    }

    /** @return BelongsTo<FeedbackIdentity,FeedbackComment> */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(FeedbackIdentity::class, 'identity_id');
    }

    /** @return BelongsTo<User,FeedbackComment> */
    public function staffUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    /** @return BelongsTo<FeedbackComment,FeedbackComment> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_comment_id');
    }

    /** @return HasMany<FeedbackComment> */
    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_comment_id')->oldest();
    }

    /** @return HasMany<FeedbackCommentVote> */
    public function votes(): HasMany
    {
        return $this->hasMany(FeedbackCommentVote::class, 'comment_id');
    }

    /** @param Builder<FeedbackComment> $query */
    public function scopeWhereTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_comment_id');
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
            ->useLogName('forge.feedback.comment')
            ->logOnly(['body', 'post_id', 'parent_comment_id', 'is_pinned', 'is_staff_reply'])
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
            'comment_id' => $this->getKey(),
        ]);
        $activity->description = 'feedback.comment.'.($activity->event ?? 'updated');
    }
}
