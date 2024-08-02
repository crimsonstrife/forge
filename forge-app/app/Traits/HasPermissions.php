<?php

namespace App\Traits;

use App\Models\PermissionGroup;
use Spatie\Permission\Traits\HasPermissions as SpatieHasPermissions;

/**
 * Trait HasPermissions
 * Extend the HasPermissions trait to consider permissions assigned through permission groups.
 * @package App\Traits
 */
trait HasPermissions
{
    use SpatieHasPermissions;

    public function getAllPermissionsAttribute()
    {
        $permissions = $this->permissions;

        // Get permissions through permission groups
        $permissionGroups = $this->permissionGroups;
        foreach ($permissionGroups as $group) {
            $permissions = $permissions->merge($group->permissions);
        }

        // Get permissions through roles (and their permission groups if necessary)
        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
            foreach ($role->permissionGroups as $group) {
                $permissions = $permissions->merge($group->permissions);
            }
        }

        return $permissions->unique('id');
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        return $this->getAllPermissionsAttribute()->contains('name', $permission);
    }
}
