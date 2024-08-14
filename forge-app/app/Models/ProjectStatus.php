<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProjectStatus
 *
 * This class represents the model for project statuses in the application.
 * It extends the base Model class and uses the HasFactory and SoftDeletes traits.
 *
 * @package App\Models
 */
class ProjectStatus extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'color',
        'is_default',
        'description'
    ];

    /**
     * Get the projects associated with the project status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'status_id', 'id');
    }
}
