<?php

namespace App\Models;

use App\Models\Issues\Issue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Issues\IssuePriority;
use App\Models\Projects\Project;
use App\Models\PivotModels\PrioritySetPriorities;
use App\Models\PivotModels\PrioritySetDefault;
use App\Traits\IsPermissible;
use Illuminate\Support\Facades\Log;

/**
 * Represents a set of priorities that can be assigned to projects or tasks.
 * Manages operations such as adding, removing, and reordering priorities,
 * as well as setting the default priority.
 *
 * @method static create(array $array)
 * @method static where(string $string, mixed $name)
 *
 * @package App\Models
 */
class PrioritySet extends Model
{
    use HasFactory;
    use SoftDeletes;
    use IsPermissible;

    protected $fillable = ['name'];

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        /**
         * The "saved" event listener for the PrioritySet model.
         *
         * This static method is triggered after a record is saved.
         *
         * @param \Illuminate\Database\Eloquent\Model $record The saved record instance.
         * @return void
         */
        static::saved(function ($record) {
            // Clear the cache when Priority Sets are saved
            cache()->forget('priority_sets.all');
        });

        // Clear the cache when Priority Sets are saved or deleted
        static::deleted(function () {
            cache()->forget('priority_sets.all');
        });
    }

    /**
     * Get the priorities associated with the priority set.
     * This relationship is found through the issue_priority_priority_set pivot table, with priority_set_id referencing the id of this model, and issue_priority_id referencing the id of the IssuePriority model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function priorities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        // The IssuePriority model is used as the related model, and the pivot table is issue_priority_priority_set
        return $this->belongsToMany(IssuePriority::class, 'issue_priority_priority_set', 'priority_set_id', 'issue_priority_id')
            ->using(PrioritySetPriorities::class)
            ->withPivot('order')
            ->orderBy('order');
    }

    /**
     * Get the active priorities.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activePriorities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrioritySetPriorities::class, 'priority_set_id')->whereNull('deleted_at');
    }

    /**
     * Get the priority set priorities.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prioritySetPriorities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrioritySetPriorities::class);
    }

    /**
     * Get the default priorities for the priority set.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function defaultPriorities(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(PrioritySetDefault::class, PrioritySetPriorities::class, 'priority_set_id', 'priority_set_issue_pair');
    }

    /**
     * Set the default priorities for the priority set.
     */
    public function setDefaultPriorities(array $priorities): void
    {
        // Remove all default priorities
        $this->defaultPriorities()->delete();

        // Add the new default priorities
        foreach ($priorities as $priority) {
            $this->defaultPriorities()->create(['priority_set_issue_pair' => $priority]);
        }
    }

    /**
     * Remove a priority from the priority set.
     */
    public function removePriority(IssuePriority $priority): void
    {
        $this->priorities()->detach($priority->id);
    }

    /**
     * Update the order of a priority in the priority set.
     */
    public function updatePriorityOrder(IssuePriority $priority, int $order): void
    {
        $this->priorities()->updateExistingPivot($priority->id, ['order' => $order]);
    }

    /**
     * Update the default priority for the priority set.
     */
    public function updateDefaultPriority(IssuePriority $priority): void
    {
        // Remove the default flag from all priorities in the set, then set the new default
        $this->priorities()->updateExistingPivot($priority->id, ['is_default' => true]);
    }

    /**
     * Get the projects that use this priority set.
     */
    public function projects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Project::class);
    }
}
