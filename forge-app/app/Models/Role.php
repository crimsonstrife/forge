<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\PermissionGroup;
use App\Traits\HasPermissions;

class Role extends SpatieRole
{
    use HasFactory;
    use HasPermissions;

    // In Role model
    public function permissionGroups()
    {
        return $this->belongsToMany(PermissionGroup::class, 'role_permission_group');
    }
}
