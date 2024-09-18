<?php

namespace App\Models\Auth;

use App\Traits\IsPermissable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Spatie\Permission\Guard;
use Laravel\Jetstream\Team;
use Spatie\Permission\Models\Permission;

/**
 * PermissionSet Model
 *
 * This model represents a permission set, which is a collection of permissions that can be assigned to users, roles, or other entities. Permission sets can be used to group permissions together and assign them to multiple entities at once.
 * Permission sets can have many permissions, and a permission set can belong to many permissions. These relationships are defined in the permission_set_permissions table.
 * Permission sets can also mark individual permissions as granted or denied for a group, by setting the muted column to true.
 * Permissions must be in the same guard as the permission set to be assigned to it.
 *
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class PermissionSet extends Model
{
    use HasFactory;
    use IsPermissable;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $guarded = [];

    /**
     * Summary of __construct
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        parent::__construct($attributes);

        $this->guarded[] = $this->primaryKey;
        $this->table = 'permission_sets';
    }

    /**
     * Summary of create
     * @return PermissionSet
     */
    public static function create(array $attributes = []): PermissionSet
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

        return static::query()->create($attributes);
    }

    /**
     * Define the relationship between PermissionSet and Permission models
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'perm_set_has_permissions')->withPivot('muted')->withTimestamps();
    }

    /**
     * Define the relationship between PermissionSet and User models
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'permission_set_user')->withTimestamps();
    }

    /**
     * Define the relationship between PermissionSet and Role models
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_set_role')->withTimestamps();
    }

    /**
     * Define the relationship between PermissionSet and Team models, if teams are enabled
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany | null
     */
    public function teams()
    {
        //check if teams are enabled
        if (config('permission.teams.enabled')) {
            return $this->belongsToMany(Team::class, 'permission_set_team')->withTimestamps();
        } else {
            return null;
        }
    }

    /**
     * Mark a permission as granted or denied for the permission set, i.e. mute or unmute the permission
     *
     * @param Permission $permission
     * @param bool $muted (default: false)
     * @throws \Exception
     */
    public function setMute(Permission $permission, bool $muted = false)
    {
        //get the guard name of the permission set
        $guardName = $this->getAttribute('guard_name');

        //check if the permission is already muted
        if ($this->permissions()->contains($permission)) {
            $this->permissions()->updateExistingPivot($permission->id, ['muted' => $muted]);
        } else {
            //attach the permission to the permission set with the muted value
            $result = $this->addPermission($permission, $muted);

            //if the permission was not added, throw an exception
            if (!$result) {
                //throw an exception if the permission is not in the same guard as the permission set, or a general exception if the permission could not be added
                if ($permission->getAttribute('guard_name') != $guardName) {
                    throw new \Exception("The permission is not in the same guard as the permission set.");
                } else {
                    throw new \Exception("The permission could not be added to the permission set.");
                }
            }
        }
    }

    /**
     * Mute a permission for the permission set
     * alias for setMute($permission, true)
     * @param Permission $permission
     */
    public function mute(Permission $permission)
    {
        $this->setMute($permission, true);
    }

    /**
     * Unmute a permission for the permission set
     * alias for setMute($permission, false)
     * @param Permission $permission
     */
    public function unmute(Permission $permission)
    {
        $this->setMute($permission, false);
    }

    /**
     * Check if a permission is in the permission set
     *
     * @param Permission $permission
     * @return bool
     */
    public function hasPermission(Permission $permission)
    {
        return $this->permissions()->where('id', $permission->id)->exists();
    }

    /**
     * Check if a permission is muted in the permission set
     *
     * @param Permission $permission
     * @return bool
     */
    public function isMuted(Permission $permission)
    {
        //check if the permission is in the permission set
        if ($this->hasPermission($permission)) {
            return $this->permissions()->where('id', $permission->id)->first()->pivot->muted;
        } else {
            return false;
        }
    }

    /**
     * Count the number of permissions in the permission set
     *
     * @return int
     */
    public function countPermissions()
    {
        return $this->permissions()->count();
    }

    /**
     * Count the number of muted permissions in the permission set
     *
     * @return int
     */
    public function countMutedPermissions()
    {
        return $this->permissions()->wherePivot('muted', true)->count();
    }

    /**
     * Count the number of unmuted permissions in the permission set
     *
     * @return int
     */
    public function countUnmutedPermissions()
    {
        return $this->permissions()->wherePivot('muted', false)->count();
    }

    /**
     * Check if the permission set is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->countPermissions() == 0;
    }

    /**
     * Count the number of users in the permission set
     *
     * @return int
     */
    public function countUsers()
    {
        return $this->users()->count();
    }

    /**
     * Count the number of roles in the permission set
     *
     * @return int
     */
    public function countRoles()
    {
        return $this->roles()->count();
    }

    /**
     * Add a permission to the permission set, if it is not already in the permission set
     * The permission must be in the same guard as the permission set
     *
     * @param Permission $permission
     * @param bool|null muted (default: false)
     * @return bool true if the permission was added, false if the permission was already in the permission set or could not be added.
     */
    public function addPermission(Permission $permission, ?bool $muted = false)
    {
        //get the guard name of the permission set
        $guardName = $this->getAttribute('guard_name');

        //check if the permission is already in the permission set
        if ($this->permissions()->contains($permission)) {
            return false;
        }

        //verify that the permission is in the same guard as the permission set
        $guardMatches = $permission->getAttribute('guard_name') == $guardName;

        //if the permission is in the same guard as the permission set, attach it
        if ($guardMatches) {
            $this->permissions()->attach($permission, ['muted' => $muted]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove a permission from the permission set
     *
     * @param Permission $permission
     * @return bool true if the permission was removed, false if the permission was not in the permission set
     */
    public function removePermission(Permission $permission)
    {
        //check if the permission is in the permission set
        if ($this->permissions()->contains($permission)) {
            $this->permissions()->detach($permission);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Find a permission set by its name (and optionally guardName).
     *
     * @param string $name
     * @param string|null $guardName
     * @return PermissionSet
     * @throws \Exception
     */
    public static function findOrCreate(string $name, ?string $guardName = null): PermissionSet
    {
        $guardName ??= Guard::getDefaultName(static::class);
        $permissionSet = static::query()->where(['name' => $name, 'guard_name' => $guardName])->first();

        if (!$permissionSet) {
            $createdPermissionSet = static::query()->create(['name' => $name, 'guard_name' => $guardName]);

            // Ensure the created permission set is of the expected type
            if (!$createdPermissionSet instanceof PermissionSet) {
                throw new \Exception("The created permission set is not of the expected type.");
            }

            return $createdPermissionSet;
        }

        return $permissionSet;
    }

    /**
     * Check if a permission is granted to the permission set.
     *
     * @param Permission $permission
     * @return bool
     */
    public function hasPermissionTo(Permission $permission)
    {
        //check if the permission is assigned to the permission set
        if ($this->permissions()->contains($permission)) {
            //check if the permission is muted in the permission set
            return $this->isMuted($permission);
        }

        return false;
    }
}
