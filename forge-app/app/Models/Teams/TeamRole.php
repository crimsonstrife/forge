<?php

namespace App\Models\Teams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsPermissible;

/**
 * Class TeamRole
 *
 * This class represents a role within a team in the application.
 * It extends the base Model class provided by the framework.
 *
 * @package App\Models\Teams
 */
class TeamRole extends Model
{
    protected $fillable = ['team_id', 'name', 'description'];

    /**
     * Get the team that this role belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the permissions associated with the team role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->belongsToMany(TeamPermission::class, 'team_role_permissions', 'role_id', 'permission_id');
    }

    /**
     * Check if the role has the specified permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions->contains('name', $permission);
    }
}
