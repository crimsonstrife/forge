<?php

namespace App\Models;

use App\Enums\MilestoneState;
use App\Enums\MilestoneType;
use App\Traits\HasRecordShares;
use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends BaseModel
{
    use HasFactory;
    use HasUuids;
    use IsPermissible;
    use HasRecordShares;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'project_id',
        'type',
        'state',
        'name',
        'description',
        'starts_at',
        'due_at',
        'released_at',
        'version',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'type' => MilestoneType::class,
            'state' => MilestoneState::class,
            'starts_at' => 'immutable_datetime',
            'due_at' => 'immutable_datetime',
            'released_at' => 'immutable_datetime',
            'meta' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class);
    }

    /** Convenience scopes */
    public function scopeReleases(Builder $q): Builder
    {
        return $q->where('type', MilestoneType::Release);
    }

    public function scopeMilestones(Builder $q): Builder
    {
        return $q->where('type', MilestoneType::Milestone);
    }
}
