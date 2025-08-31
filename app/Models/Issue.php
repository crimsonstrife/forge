<?php

namespace App\Models;

use App\Support\ActivityContext;
use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
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
        'starts_at',
        'due_at',
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
        'starts_at' => 'immutable_datetime',
        'due_at'    => 'immutable_datetime',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function (Issue $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

            // Generate sequential number per project and human key: {PROJECTKEY}-{number}
            // Races are prevented by DB unique constraints; in rare collision, let the controller retry.
            if (empty($model->number) || empty($model->key)) {
                $projectKey = $model->project()->value('key'); // lightweight single column query

                $next = (int) (static::query()
                        ->where('project_id', $model->project_id)
                        ->max('number') ?? 0) + 1;

                $model->number = $model->number ?: $next;
                $model->key = $model->key ?: "{$projectKey}-{$model->number}";
            }
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
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(IssueType::class, 'issue_type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(IssueStatus::class, 'issue_status_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(IssuePriority::class, 'issue_priority_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->orderBy('created_at');
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }

    public function goals(): MorphToMany
    {
        return $this->morphToMany(Goal::class, 'linkable', 'goal_links');
    }

    public function timeEntries(): HasMany
    {
        /** @return HasMany<TimeEntry> */
        return $this->hasMany(TimeEntry::class);
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

    public function scopeWithMeta($q)
    {
        return $q->with([
            'status:id,name,color,is_done',
            'type:id,key,name',
            'priority:id,name',
            'assignee:id,name,profile_photo_path',
            'reporter:id,name,profile_photo_path',
            'project:id,key',
            'tags',
        ])->withCount([
            'comments',
            // Count only 'attachments' collection in media
            'media as attachments_count' => fn ($m) => $m->where('collection_name', 'attachments'),
        ]);
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

    /**
     * Sum of tracked seconds for this issue.
     * Includes running timers as of "now".
     */
    public function totalTrackedSeconds(): int
    {
        $ended = $this->timeEntries()
            ->whereNotNull('ended_at')
            ->sum('duration_seconds');

        $running = $this->timeEntries()
            ->whereNull('ended_at')
            ->get()
            ->sum(fn ($t) => now()->diffInSeconds($t->started_at));

        return (int) ($ended + $running);
    }
}
