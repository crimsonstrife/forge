<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\PermissionGroup;
use App\Traits\HasPermissions;

/**
 * Role Model
 *
 * This class represents a role in the application.
 * It extends the SpatieRole class and uses the HasFactory and HasPermissions traits.
 *
 * @package App\Models
 */
class Role extends SpatieRole
{
    use HasFactory;
    use HasPermissions;

    /**
     * Get the permission groups associated with the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissionGroups()
    {
        return $this->belongsToMany(PermissionGroup::class, 'role_permission_group');
    }
}
