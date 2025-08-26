<?php

namespace App\Services\Auth;

use App\Models\Role;
use Illuminate\Support\Collection;

final class RolePermissionSync
{
    public function syncRole(Role $role): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection $direct */
        $direct = $role->permissions()->pluck('name');

        /** @var Collection $fromSets */
        $fromSets = $role->permissionSets()       // role ↔ permission_sets
        ->with('permissions:id,name')        // set ↔ permissions
        ->get()
            ->flatMap(fn ($set) => $set->permissions->pluck('name'));

        /** @var Collection $mutes */
        $mutes = $role->permissionSets()
            ->with('mutedPermissions:id,name')
            ->get()
            ->flatMap(fn ($set) => $set->mutedPermissions->pluck('name'));

        $final = $direct->merge($fromSets)->diff($mutes)->unique()->values();

        $role->syncPermissions($final->all());
    }
}
