<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsPermissible;

/**
 * Class ProjectPermission
 *
 * This class represents the permissions associated with a project.
 * It extends the base Model class provided by the framework.
 *
 * @package App\Models\Projects
 */
class ProjectPermission extends Model
{
    use HasFactory;
    use IsPermissible;

    protected $fillable = ['project_id', 'name', 'description', 'guard_name'];

    /**
     * Get the project associated with the permission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
