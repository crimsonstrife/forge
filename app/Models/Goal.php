<?php

namespace App\Models;

use App\Enums\GoalStatus;
use App\Enums\GoalType;
use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property-read \Illuminate\Support\Collection<int, GoalKeyResult> $keyResults
 */
#[ObservedBy([\App\Observers\GoalObserver::class])]
class Goal extends Model
{
    use HasFactory;
    use IsPermissible;

    protected $fillable = [
        'owner_type','owner_id','parent_id','name','description','goal_type','status',
        'start_date','due_date','progress','created_by','meta',
    ];

    protected $casts = [
        'goal_type' => GoalType::class,
        'status' => GoalStatus::class,
        'start_date' => 'date',
        'due_date' => 'date',
        'meta' => 'array',
        'progress' => 'float',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    public function keyResults(): HasMany
    {
        return $this->hasMany(GoalKeyResult::class);
    }
    public function links(): HasMany
    {
        return $this->hasMany(GoalLink::class);
    }

    /** @return MorphToMany<Project> */
    public function projects(): MorphToMany
    {
        return $this->morphedByMany(Project::class, 'linkable', 'goal_links');
    }

    /** @return MorphToMany<Issue> */
    public function issues(): MorphToMany
    {
        return $this->morphedByMany(Issue::class, 'linkable', 'goal_links');
    }
}
