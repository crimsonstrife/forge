<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsPermissible;

/**
 * Class ProjectUserRole
 *
 * This class represents the relationship between a project and a user role within the application.
 * It extends the base Model class provided by the framework.
 *
 * @package App\Models\Projects
 */
class ProjectUserRole extends Model
{
    protected $fillable = ['project_id', 'name', 'description'];

    /**
     * Get the project associated with the user role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the permissions associated with the project user role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->belongsToMany(ProjectPermission::class, 'project_role_permissions', 'role_id', 'permission_id');
    }

    /**
     * Check if the role has the specified permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions->contains('name', $permission);
    }
}
