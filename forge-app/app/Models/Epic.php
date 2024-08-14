<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Epic
 * Model for the Epic table
 * @package App\Models
 */
class Epic extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'project_id',
        'start_date',
        'end_date',
        'project_id'
    ];

    protected $dates = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    /**
     * Get the Project that owns the Epic
     * Use the project_id column to match the id column in the Project model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    /**
     * Get all of the Issues for the Epic
     * Use the id column in the Epic model to match the epic_id column in the Issue model
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'epic_id', 'id');
    }

    /**
     * Get the Sprint associated with the Epic
     * Use the id column in the Epic model to match the epic_id column in the Sprint model
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sprint(): HasOne
    {
        return $this->hasOne(Sprint::class, 'epic_id', 'id');
    }

    /**
     *
     * Get all of the Stories associated with the Epic
     * Use the id column in the Epic model to match the epic_id column in the Story model
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stories(): HasMany
    {
        return $this->hasMany(Story::class, 'epic_id', 'id');
    }

    /**
     * Get the parent Epic of the Epic if it exists
     * Use the parent_id column in the Epic model to match the id column in the Epic model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Epic::class, 'parent_id', 'id');
    }

    /**
     * Get all of the child Epics of the Epic
     * Use the id column in the Epic model to match the parent_id column in the Epic model
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Epic::class, 'id', 'parent_id');
    }
}
