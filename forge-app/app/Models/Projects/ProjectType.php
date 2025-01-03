<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Projects\Project;
use App\Traits\IsPermissible;

/**
 * ProjectType Model
 *
 * Represents a project type in the application.
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property string $description
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Projects\Project[] $projects
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectType newQuery()
 * @method static \Illuminate\Database\Query\Builder|ProjectType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ProjectType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ProjectType withoutTrashed()
 */
class ProjectType extends Model
{
    use HasFactory;
    use SoftDeletes;
    use IsPermissible;

    protected $fillable = [
        'name',
        'color',
        'description',
        'is_default'
    ];

    /**
     * Get the projects associated with the project type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'type_id', 'id');
    }

    /**
     * Is the project type the default type?
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Get the color of the project type
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Get the description of the project type
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
