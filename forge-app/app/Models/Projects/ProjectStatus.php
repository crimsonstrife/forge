<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Projects\Project;
use App\Traits\IsPermissible;

/**
 * Class ProjectStatus
 *
 * This class represents the model for project statuses in the application.
 * It extends the base Model class and uses the HasFactory and SoftDeletes traits.
 *
 * @package App\Models\Projects
 */
class ProjectStatus extends Model
{
    use HasFactory;
    use SoftDeletes;
    use IsPermissible;

    protected $fillable = [
        'name',
        'color',
        'is_default',
        'description'
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        // Clear the cache when Project Statuses are saved or deleted
        static::saved(function () {
            cache()->forget('project_statuses.all');
        });

        // Clear the cache when Project Statuses are saved or deleted
        static::deleted(function () {
            cache()->forget('project_statuses.all');
        });

        /**
         * Event handler for the "saved" event of the ProjectStatus model.
         *
         * This event handler is triggered when an ProjectStatus model is saved.
         * It performs certain actions based on the saved model's properties.
         * If the saved model is marked as the default status, it updates all other ProjectStatus models
         * to set their "is_default" property to false, except for the current model.
         *
         * @param \App\Models\Projects\ProjectStatus $item
         * @return void
         */
        static::saved(function (ProjectStatus $item) {
            if ($item->is_default) {
                $query = ProjectStatus::where('id', '<>', $item->id)
                    ->where('is_default', true);

                // Update all other statuses to set their "is_default" property to false, except for the current model
                $query->update(['is_default' => false]);
            }
        });
    }

    /**
     * Get the projects associated with the project status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'status_id', 'id');
    }

    /**
     * Get the default project status.
     *
     * @return ProjectStatus
     */
    public static function getDefaultStatus(): ProjectStatus
    {
        return self::where('is_default', true)->first();
    }

    /**
     * Get the project status by name.
     *
     * @param string $name
     * @return ProjectStatus
     */
    public static function getByName(string $name): ProjectStatus
    {
        return self::where('name', $name)->first();
    }

    /**
     * Get the project status by ID.
     *
     * @param int $id
     * @return ProjectStatus
     */
    public static function getById(int $id): ProjectStatus
    {
        return self::where('id', $id)->first();
    }
}
