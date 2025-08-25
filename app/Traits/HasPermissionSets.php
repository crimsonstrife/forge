<?php

namespace App\Traits;

use App\Models\PermissionSet;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermissionSets
{
    /** @return BelongsToMany<PermissionSet> */
    public function permissionSets(): BelongsToMany
    {
        return $this->belongsToMany(PermissionSet::class, 'user_permission_sets');
    }
}
