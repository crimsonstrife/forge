<?php

namespace App\Models;

use App\Support\ActivityContext;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    protected $casts = ['id' => 'string'];
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
                'summary','description','status_id','type_id','priority_id',
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

    public function parent(): BelongsTo { return $this->belongsTo(__CLASS__, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(__CLASS__, 'parent_id'); }

    public function scopeEpics($q) { return $q->whereHas('type', fn($t) => $t->where('key','EPIC')); }
    public function scopeStories($q) { return $q->whereHas('type', fn($t) => $t->where('key', 'STORY')); }
    public function scopeBugs($q) { return $q->whereHas('type', fn($t) => $t->where('key', 'BUG')); }
    public function scopeTasks($q) { return $q->whereHas('type', fn($t) => $t->where('key', 'TASK')); }
    public function scopeSubTasks($q) { return $q->whereHas('type', fn($t) => $t->where('key', 'SUBTASK')); }
}
