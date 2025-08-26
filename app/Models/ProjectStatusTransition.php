<?php

namespace App\Models;

use App\Support\ActivityContext;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectStatusTransition extends Model
{
    use HasUuids;
    use LogsActivity;

    protected $keyType = 'string';
    public $incrementing = false;

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
            ->logOnly(['project_id','from_status_id','to_status_id'])
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
        $activity->description = 'project.status.' . $activity->event;
    }
}
