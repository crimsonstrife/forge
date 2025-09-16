<?php

namespace App\Models;

use App\Enums\SprintState;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sprint extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'project_id', 'name', 'goal', 'start_date', 'end_date', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'state' => SprintState::class,
            'start_date' => 'date',
            'end_date'   => 'date',
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

    /** @return Builder<static> */
    public function scopeForProject(Builder $q, string $projectId): Builder
    {
        return $q->where('project_id', $projectId);
    }

    /** @return Builder<static> */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('state', SprintState::Active);
    }

    /** @return Builder<static> */
    public function scopePlanned(Builder $q): Builder
    {
        return $q->where('state', SprintState::Planned);
    }

    /** @return Builder<static> */
    public function scopeClosed(Builder $q): Builder
    {
        return $q->where('state', SprintState::Closed);
    }
}
