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

    /**
     * Get all permissions attribute.
     *
     * This method retrieves all permissions associated with the current model instance.
     * It includes permissions directly assigned to the model, as well as permissions
     * inherited through permission groups and roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
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

    /**
     * Check if the user has a specific permission.
     *
     * @param string $permission The name of the permission to check.
     * @param string|null $guardName The name of the guard to use (optional).
     * @return bool Returns true if the user has the permission, false otherwise.
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        return $this->getAllPermissionsAttribute()->contains('name', $permission);
    }
}
