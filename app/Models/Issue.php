<?php

namespace App\Models;

use App\Support\ActivityContext;
use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

class Issue extends BaseModel implements HasMedia
{
    use HasUuids;
    use HasTags;
    use LogsActivity;
    use InteractsWithMedia;
    use IsPermissible;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'project_id',
        'parent_id',
        'issue_type_id',
        'issue_status_id',
        'issue_priority_id',
        'reporter_id',
        'assignee_id',
        'story_points',
        'estimate_minutes',
        'description',
        'summary',
    ];

    protected $guarded = ['id', 'key', 'number'];

    protected $casts = [
        'id' => 'string',
        'number' => 'integer',
        'parent_id' => 'string',
        'project_id' => 'string',
        'reporter_id' => 'string',
        'assignee_id' => 'string',
        'story_points' => 'int',
        'children_count' => 'int',
        'children_done_count' => 'int',
        'children_points_total' => 'int',
        'children_points_done' => 'int',
        'progress_percent' => 'int',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('forge.issue')
            ->logOnly([
                'summary','description','issue_status_id','issue_type_id','priority_id',
                'assignee_id','reporter_id','story_points','estimate_minutes','parent_id'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(\Spatie\Activitylog\Contracts\Activity $activity): void
    {
        $ctx = ActivityContext::base();
        $activity->team_id = $ctx['team_id'];
        $activity->properties = $activity->properties->merge([
            'actor_id' => $ctx['user_id'],
            'ip'       => $ctx['ip'],
            'ua'       => $ctx['user_agent'],
            'issue_id' => $this->getKey(),
            'project_id' => $this->project_id,
        ]);

        // Make status transitions explicit
        if ($activity->event === 'updated' && $activity->properties->has('attributes.status_id')) {
            $activity->event = 'status_changed';
            $activity->description = 'issue.status_changed';
        } else {
            $activity->description = 'issue.' . ($activity->event ?? 'updated');
        }
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id');
    }

    public function type(): HasOne
    {
        return $this->HasOne(IssueType::class, 'id');
    }

    public function status(): HasOne
    {
        return $this->HasOne(IssueStatus::class, 'id');
    }

    public function priority(): HasOne
    {
        return $this->HasOne(IssuePriority::class, 'id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function reporter(): HasOne
    {
        return $this->HasOne(User::class, 'id', 'reporter_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->orderBy('created_at');
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }

    public function assignee(): HasOne
    {
        return $this->HasOne(User::class, 'id', 'assignee_id');
    }

    public function scopeEpics($q)
    {
        return $q->whereHas('type', fn ($t) => $t->where('key', 'EPIC'));
    }

    public function scopeStories($q)
    {
        return $q->whereHas('type', fn ($t) => $t->where('key', 'STORY'));
    }

    public function scopeBugs($q)
    {
        return $q->whereHas('type', fn ($t) => $t->where('key', 'BUG'));
    }

    public function scopeTasks($q)
    {
        return $q->whereHas('type', fn ($t) => $t->where('key', 'TASK'));
    }

    public function scopeSubTasks($q)
    {
        return $q->whereHas('type', fn ($t) => $t->where('key', 'SUBTASK'));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->useDisk('attachments')       // private
            ->singleFile(false);
    }

    /** Percent as float 0..1 for charts */
    public function progress(): float
    {
        return max(0, min(1, $this->progress_percent / 100));
    }

    public function getRouteKeyName(): string
    {
        return 'key';
    }
}
