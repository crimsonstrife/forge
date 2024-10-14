<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\PermissionGroup;
use App\Traits\HasAdvancedPermissions;
use App\Traits\IsPermissable;

/**
 * Class Team
 *
 * This class represents a team model in the application.
 * It extends the base Model class and uses the HasFactory and HasPermissions traits.
 */
class Team extends Model
{
    use HasFactory;
    use HasAdvancedPermissions;
    use IsPermissable;

    /**
     * Get the permission groups associated with the team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissionGroups()
    {
        return $this->belongsToMany(PermissionGroup::class, 'team_permission_group');
    }
}
