<?php

namespace App\Models;

use App\Support\ActivityContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Comment extends BaseModel implements HasMedia
{
    use HasUuids;
    use LogsActivity;
    use InteractsWithMedia;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'issue_id',
        'user_id',
        'body'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    // Polymorphic target (Issue, Attachment, etc.)
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    // Threading
    public function parent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id')->orderBy('created_at');
    }

    // replies relation alias for clarity
    public function replies(): HasMany
    {
        return $this->children();
    }

    // Convenience scope for listing under a model
    public function scopeFor($q, Model $model): Builder
    {
        /** @var Builder $q */
        return $q->whereMorphedTo('commentable', $model);
    }

    // Media Library
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')->useDisk('attachments');
    }

    // Activity logging
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('forge.comment')
            ->logOnly(['body'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(\Spatie\Activitylog\Contracts\Activity $activity): void
    {
        $ctx = ActivityContext::base();
        $activity->team_id = $ctx['team_id'];
        $activity->properties = $activity->properties->merge([
            'actor_id'   => $ctx['user_id'],
            'ip'         => $ctx['ip'],
            'ua'         => $ctx['user_agent'],
            'comment_id' => $this->id,
            'context_media_id' => $this->context_media_id,
            'commentable' => [
                'type' => $this->commentable_type,
                'id'   => $this->commentable_id,
            ],
        ]);
        $activity->description = 'comment.' . ($activity->event ?? 'updated');
    }
}
