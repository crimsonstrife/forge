<?php

namespace App\Traits;

use Illuminate\Container\Attributes\Log;
use Spatie\Permission\Traits\HasRoles;

trait HasAdvancedPermissions
{
    use HasRoles;

    /**
     * Override the hasPermissionTo method to handle advanced permission logic.
     *
     * @param string|int|\Spatie\Permission\Models\Permission $permission
     * @return bool
     */
    public function hasPermissionTo($permission)
    {
        // Eager load the Permission objects/relations before checking
        $this->load('permissions', 'permissionSets.permissions', 'permissionGroups.permissions', 'permissionGroups.permissions');

        // Check if any PermissionSet has the given permission and it is not muted
        $mutedPermissions = $this->getMutedPermissions();
        // If there are muted permissions, log them
        if ($mutedPermissions->isNotEmpty()) {
            logger()->info('Muted permissions: ' . $mutedPermissions->implode(', '));
        }

        // If permission is muted for the user, deny access
        if ($mutedPermissions->contains($permission)) {
            // Log the muted permission
            logger()->info('Permission is muted: ' . $permission);
            return false;
        }

        // Check if user has permission directly
        if ($this->permissions->contains('name', $permission)) {
            // Log the permission
            logger()->info('Permission found: ' . $permission . ' (directly)');
            return true;
        }

        // Check if permission exists in PermissionSets or PermissionGroups
        if($this->hasPermissionViaSetsOrGroups($permission)) {
            // Log the permission
            logger()->info('Permission found: ' . $permission . ' (via PermissionSets or PermissionGroups)');
            return true;
        }

        // If the permission is not found in Permissions, PermissionSets or PermissionGroups, deny access
        return false;
    }

    /**
     * Get a list of muted permissions from the permission sets.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMutedPermissions()
    {
        // Get all muted permissions from PermissionSets
        $mutedPermissions = $this->permissionSets->flatMap->permissions->where('pivot.muted', true)->pluck('name');

        // Get all muted permissions from PermissionSets in PermissionGroups
        $mutedPermissionsFromGroupSets = $this->permissionGroups->flatMap->permissionSets->flatMap->permissions->where('pivot.muted', true)->pluck('name');

        // Merge the muted permissions from PermissionSets and PermissionGroups, and return the unique values
        return $mutedPermissions->merge($mutedPermissionsFromGroupSets)->unique();
    }

    /**
     * Check if the user has a permission via their PermissionSets or PermissionGroups.
     *
     * @param string|int|\Spatie\Permission\Models\Permission $permission
     * @return bool
     */
    public function hasPermissionViaSetsOrGroups($permission)
    {
        // Check if user has the permission via PermissionSets
        $permissionFromSet = $this->permissionSets->flatMap->permissions->pluck('name')->contains($permission);
        if ($permissionFromSet) {
            return true;
        }

        // Check if user has the permission via PermissionGroups
        $permissionFromGroup = $this->permissionGroups->flatMap->permissions->pluck('name')->contains($permission);
        if ($permissionFromGroup) {
            return true;
        }

        // Check PermissionSets in the PermissionGroups
        $permissionFromGroupSets = $this->permissionGroups->flatMap->permissionSets->flatMap->permissions->pluck('name')->contains($permission);
        if ($permissionFromGroupSets) {
            return true;
        }

        // If the permission is not found in Permissions, PermissionSets or PermissionGroups, deny access
        return false;
    }

    /**
     * Define relationship with PermissionSets.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissionSets()
    {
        return $this->belongsToMany(\App\Models\PermissionSet::class, 'permission_set_user');
    }

    /**
     * Define relationship with PermissionGroups.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissionGroups()
    {
        return $this->belongsToMany(\App\Models\PermissionGroup::class, 'permission_group_user');
    }
}
