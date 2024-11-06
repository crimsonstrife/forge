<?php

namespace App\Models;

use App\Models\Issues\Issue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Issues\IssuePriority;
use App\Models\Projects\Project;
use App\Models\PivotModels\PrioritySetPriorities;
use App\Traits\IsPermissable;
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
    use IsPermissable;

    protected $fillable = ['name'];

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear the cache when Priority Sets are saved or deleted
        static::saved(function () {
            cache()->forget('priority_sets.all');
        });

        // Clear the cache when Priority Sets are saved or deleted
        static::deleted(function () {
            cache()->forget('priority_sets.all');
        });

        static::saving(function ($model) {
            $query = static::where('name', $model->name);

            if ($model->exists) {
                $query->where('id', '!=', $model->id); // Exclude the current model from the query
            }

            // If a priority set with the same name already exists, prevent the model from being saved
            if ($query->exists()) {
                return false; // Prevent the model from being saved
            }

            // If the model is being saved, return true to allow the save operation to continue
            return true;
        });
    }

    /**
     * Before saving the PrioritySet
     * This method is used to ensure the priority set is setup correctly before saving it.
     *
     * @param $record
     */
    public function beforeSave($record): void
    {
        // If the priority set is being created,
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
                    ->withPivot('order', 'is_default')
                    ->orderBy('order');
    }

    public function prioritySetPriorities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrioritySetPriorities::class);
    }

    /**
     * Get the default priority for the priority set.
     */
    public function defaultPriority()
    {
        return $this->priorities(IssuePriority::class, 'issue_priority_priority_set')
                    ->wherePivot('is_default', true)
                    ->first();
    }

    /**
     * Set the default priority for the priority set.
     */
    public function setDefaultPriority(IssuePriority $priority): void
    {
        // Remove the default flag from all priorities in the set, then set the new default
        $this->priorities()->updateExistingPivot($priority->id, ['is_default' => true]);
    }

    /**
     * Add a priority to the priority set.
     */
    public function addPriority(IssuePriority $priority, int $order = 0, bool $isDefault = false): void
    {
        $this->priorities()->attach($priority->id, ['order' => $order, 'is_default' => $isDefault]);
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
