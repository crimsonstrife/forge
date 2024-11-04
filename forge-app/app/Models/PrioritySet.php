<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Issues\IssuePriority;
use App\Models\Projects\Project;
use App\Traits\IsPermissable;

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
    protected static function boot()
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

            if ($query->exists()) {
                return false; // Prevent the model from being saved
            }

            return true;
        });
    }

    /**
     * Get the priorities associated with the priority set.
     */
    public function priorities()
    {
        return $this->belongsToMany(IssuePriority::class)
                    ->withPivot('order')
                    ->orderBy('order');
    }

    /**
     * Get the default priority for the priority set.
     */
    public function defaultPriority()
    {
        return $this->priorities()
                    ->wherePivot('is_default', true)
                    ->first();
    }

    /**
     * Get the issue priorities associated with the priority set.
     */
    public function issuePriorities()
    {
        return $this->belongsToMany(IssuePriority::class)
                    ->withPivot('order', 'is_default')
                    ->orderBy('order');
    }

    /**
     * Set the default priority for the priority set.
     */
    public function setDefaultPriority(IssuePriority $priority)
    {
        // Remove the default flag from all priorities in the set, then set the new default
        $this->priorities()->updateExistingPivot($priority->id, ['is_default' => true]);
    }

    /**
     * Add a priority to the priority set.
     */
    public function addPriority(IssuePriority $priority, int $order = 0, bool $isDefault = false)
    {
        $this->priorities()->attach($priority->id, ['order' => $order, 'is_default' => $isDefault]);
    }

    /**
     * Remove a priority from the priority set.
     */
    public function removePriority(IssuePriority $priority)
    {
        $this->priorities()->detach($priority->id);
    }

    /**
     * Update the order of a priority in the priority set.
     */
    public function updatePriorityOrder(IssuePriority $priority, int $order)
    {
        $this->priorities()->updateExistingPivot($priority->id, ['order' => $order]);
    }

    /**
     * Update the default priority for the priority set.
     */
    public function updateDefaultPriority(IssuePriority $priority)
    {
        // Remove the default flag from all priorities in the set, then set the new default
        $this->priorities()->updateExistingPivot($priority->id, ['is_default' => true]);
    }

    /**
     * Get the projects that use this priority set.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
