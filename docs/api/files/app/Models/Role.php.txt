<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PermissionGroup;
use App\Traits\HasPermissions;

class Role extends Model
{
    use HasFactory;
    use HasPermissions;

    // In Role model
    public function permissionGroups()
    {
        return $this->belongsToMany(PermissionGroup::class, 'role_permission_group');
    }
}
