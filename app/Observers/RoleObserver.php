<?php

namespace App\Observers;

use App\Models\Role;
use App\Services\Auth\RolePermissionSync;

class RoleObserver
{
    public function saved(Role $role): void
    {
        app(RolePermissionSync::class)->syncRole($role);
    }
    public function pivotAttached(Role $role): void
    {
        app(RolePermissionSync::class)->syncRole($role);
    }
    public function pivotDetached(Role $role): void
    {
        app(RolePermissionSync::class)->syncRole($role);
    }
}
