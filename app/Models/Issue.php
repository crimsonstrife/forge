<?php

namespace App\Models;

use App\Support\ActivityContext;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Tags\HasTags;

class Issue extends Model
{
    use HasUuids;
    use HasTags;
    use LogsActivity;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $attributes = [
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
        'summary'
    ];

    protected $casts = [
        'id' => 'string',
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
                'summary','description','issue_status_id','type_id','priority_id',
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
        return $this->HasOne(IssueType::class, 'issue_type_id');
    }

    public function status(): HasOne
    {
        return $this->HasOne(IssueStatus::class, 'issue_status_id');
    }

    public function priority(): HasOne
    {
        return $this->HasOne(IssuePriority::class, 'issue_priority_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function reporter(): HasOne
    {
        return $this->HasOne(User::class, 'reporter_id');
    }

    public function assignee(): HasOne
    {
        return $this->HasOne(User::class, 'assignee_id');
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

    /** Percent as float 0..1 for charts */
    public function progress(): float
    {
        return max(0, min(1, $this->progress_percent / 100));
    }
}
