<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

/**
 * Class PermissionGroup
 *
 * This class represents a permission group in the application.
 * It extends the base Model class and uses the HasFactory trait.
 *
 * @package App\Models
 */
class PermissionGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get the permissions associated with the permission group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_group_permission');
    }
}
