<?php

namespace App\Models\Teams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\PermissionGroup;
use App\Traits\HasAdvancedPermissions;
use App\Traits\IsPermissible;

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
    use IsPermissible;

    protected $fillable = ['team_id', 'name', 'description'];

    /**
     * Get the members that belong to the team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->using(TeamMember::class)
            ->withPivot(['role_id'])
            ->withTimestamps();
    }

    /**
     * Check if the team has a member with the given user ID.
     *
     * @param int $userId The ID of the user to check.
     * @return bool True if the user is a member of the team, false otherwise.
     */
    public function hasMember($userId): bool
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    /**
     * Get the team associated with the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the permissions associated with the team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->HasMany(TeamPermission::class, 'team_role_permissions', 'role_id', 'permission_id');
    }

    /**
     * Get the roles associated with the team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->hasMany(TeamRole::class);
    }
}
