<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PermissionGroup;
use App\Traits\HasPermissions;

class Team extends Model
{
    use HasFactory;
    use HasPermissions;

    public function permissionGroups()
    {
        return $this->belongsToMany(PermissionGroup::class, 'team_permission_group');
    }
}
