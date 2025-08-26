<?php

namespace App\Models;

use App\Enums\ProjectStage;
use App\Support\ActivityContext;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Project extends Model
{
    use HasUuids;
    use LogsActivity;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'settings',
        'organization_id',
        'lead_id'
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'lead_id' => 'string',
        'settings' => 'json',
        'stage' => ProjectStage::class,
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'archived_at' => 'datetime',
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
            ->useLogName('forge.project')
            ->logOnly(['name','key','description','lead_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(\Spatie\Activitylog\Contracts\Activity $activity): void
    {
        $ctx = ActivityContext::base();
        $activity->team_id = $ctx['team_id'];                 // persisted column
        $activity->properties = $activity->properties->merge([ // JSON props
            'actor_id' => $ctx['user_id'],
            'ip'       => $ctx['ip'],
            'ua'       => $ctx['user_agent'],
        ]);
        $activity->event = $activity->event ?: 'updated'; // create/update/delete auto-populate
        $activity->description = 'project.' . $activity->event;
    }

    public function issueTypes(): BelongsToMany
    {
        return $this->belongsToMany(IssueType::class, 'project_issue_types')
            ->withPivot(['order','is_default'])
            ->orderBy('project_issue_types.order');
    }

    public function issueStatuses(): BelongsToMany
    {
        return $this->belongsToMany(IssueStatus::class, 'project_issue_statuses')
            ->withPivot(['order','is_initial','is_default_done'])
            ->orderBy('project_issue_statuses.order');
    }

    public function issuePriorities(): BelongsToMany
    {
        return $this->belongsToMany(IssuePriority::class, 'project_issue_priorities')
            ->withPivot(['order','is_default'])
            ->orderBy('project_issue_priorities.order');
    }

    public function statusTransitions()
    {
        return $this->hasMany(ProjectStatusTransition::class);
    }

    /** IDs / selections (with global fallback) */
    public function allowedTypeIds(): array
    {
        $ids = $this->issueTypes()->pluck('issue_types.id')->map(fn($id)=>(int)$id)->all();
        return $ids ?: IssueType::query()->pluck('id')->map(fn($id)=>(int)$id)->all();
    }

    public function allowedStatusIds(): array
    {
        $ids = $this->issueStatuses()->pluck('issue_statuses.id')->map(fn($id)=>(int)$id)->all();
        return $ids ?: IssueStatus::query()->pluck('id')->map(fn($id)=>(int)$id)->all();
    }

    public function allowedPriorityIds(): array
    {
        $ids = $this->issuePriorities()->pluck('issue_priorities.id')->map(fn($id)=>(int)$id)->all();
        return $ids ?: IssuePriority::query()->pluck('id')->map(fn($id)=>(int)$id)->all();
    }

    public function defaultTypeId(): ?int
    {
        return $this->issueTypes()->wherePivot('is_default', true)->value('issue_types.id')
            ?? (int) IssueType::query()->where('is_default', true)->value('id');
    }

    public function defaultPriorityId(): ?int
    {
        return $this->issuePriorities()->wherePivot('is_default', true)->value('issue_priorities.id')
            ?? (int) IssuePriority::query()->where('weight', 50)->orWhere('is_default', true)->value('id');
    }

    public function initialStatusId(): ?int
    {
        $id = $this->issueStatuses()->wherePivot('is_initial', true)->value('issue_statuses.id');
        if ($id) { return (int) $id; }

        // global fallback, but only if it's included in project’s allowed set
        $fallback = IssueStatus::query()->where('is_done', false)->orderBy('order')->value('id');
        return in_array((int)$fallback, $this->allowedStatusIds(), true) ? (int)$fallback : null;
    }

    public function canTransition(string $fromStatusId, string $toStatusId, ?string $issueTypeId = null): bool
    {
        $q = $this->statusTransitions()
            ->where('from_status_id', $fromStatusId)
            ->where('to_status_id', $toStatusId);

        if ($issueTypeId) {
            $q->where(fn($w)=> $w->where('issue_type_id', $issueTypeId)->orWhereNull('issue_type_id'));
        }

        return $q->exists();
    }
}
