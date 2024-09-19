<?php

namespace App\Models\Auth;

use App\Traits\IsPermissable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Contracts\Role as RoleContract;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Guard;
use App\Models\Auth\PermissionRegistrar as PermissionRegistrar;
use App\Traits\HasAdvancedPermissions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\RefreshesPermissionCache;

/**
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Role extends SpatieRole implements RoleContract
{
    use IsPermissable;
    use HasAdvancedPermissions;
    use RefreshesPermissionCache;

    protected $fillable = ['name', 'protected'];

    protected $guarded = [];

    /**
     * Prevent deletion of the role if it is protected.
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($role) {
            if ($role->protected) {
                throw new \Exception("The {$role->name} role cannot be deleted for security reasons.");
            }
        });
    }

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        parent::__construct($attributes);

        $this->guarded[] = $this->primaryKey;
        $this->table = config('permission.table_names.roles') ?: parent::getTable();
    }

    /**
     * @return RoleContract|Role
     *
     * @throws RoleAlreadyExists
     */
    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

        $params = ['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']];
        if (app(PermissionRegistrar::class)->teams) {
            $teamsKey = app(PermissionRegistrar::class)->teamsKey;

            if (array_key_exists($teamsKey, $attributes)) {
                $params[$teamsKey] = $attributes[$teamsKey];
            } else {
                $attributes[$teamsKey] = getPermissionsTeamId();
            }
        }
        if (static::findByParam($params)) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        $createdRole = static::query()->create($attributes);

        // Ensure the created role is of the expected type
        if (!$createdRole instanceof RoleContract || !$createdRole instanceof Role) {
            throw new \Exception("The created role is not of the expected type.");
        }

        return $createdRole;
    }

    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    /**
     * A role may be assigned to various permission sets.
     */
    public function permissionSets(): BelongsToMany
    {
        return $this->belongsToMany(PermissionSet::class, 'permission_set_role', 'role_id', 'permission_set_id');
    }

    /**
     * A role may be assigned to various permission groups.
     */
    public function permissionGroups(): BelongsToMany
    {
        return $this->belongsToMany(PermissionGroup::class, 'permission_group_role', 'role_id', 'permission_group_id');
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name'] ?? config('auth.defaults.guard')),
            'model',
            config('permission.table_names.model_has_roles'),
            app(PermissionRegistrar::class)->pivotRole,
            config('permission.column_names.model_morph_key')
        );
    }

    /**
     * Find a role by its name and guard name.
     *
     * @return RoleContract|Role
     *
     * @throws RoleDoesNotExist
     */
    public static function findByName(string $name, ?string $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (!$role) {
            throw RoleDoesNotExist::named($name, $guardName);
        }

        return $role;
    }

    /**
     * Find a role by its id (and optionally guardName).
     *
     * @return RoleContract|Role
     */
    public static function findById(int|string $id, ?string $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::findByParam([(new static())->getKeyName() => $id, 'guard_name' => $guardName]);

        if (!$role) {
            throw RoleDoesNotExist::withId($id, $guardName);
        }

        return $role;
    }

    /**
     * Find or create role by its name (and optionally guardName).
     *
     * @return RoleContract|Role
     */
    public static function findOrCreate(string $name, ?string $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (!$role) {
            $createdRole = static::query()->create(['name' => $name, 'guard_name' => $guardName] + (app(PermissionRegistrar::class)->teams ? [app(PermissionRegistrar::class)->teamsKey => getPermissionsTeamId()] : []));

            // Ensure the created role is of the expected type
            if (!$createdRole instanceof RoleContract || !$createdRole instanceof Role) {
                throw new \Exception("The created role is not of the expected type.");
            }

            return $createdRole;
        }

        return $role;
    }

    /**
     * Finds a role based on an array of parameters.
     *
     * @return RoleContract|Role|null
     */
    protected static function findByParam(array $params = []): ?RoleContract
    {
        $query = static::query();

        if (app(PermissionRegistrar::class)->teams) {
            $teamsKey = app(PermissionRegistrar::class)->teamsKey;

            $query->where(
                fn ($q) => $q->whereNull($teamsKey)
                    ->orWhere($teamsKey, $params[$teamsKey] ?? getPermissionsTeamId())
            );
            unset($params[$teamsKey]);
        }

        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }

    /**
     * See if the role has a permission directly.
     *
     * @param \App\Models\UserAuth\Permission $permission
     * @return bool
     */
    public function hasDirectPermission($permission): bool
    {
        return $this->permissions()->where('id', $permission->id)->exists();
    }

    /**
     * See if the role has a permission through a permission set.
     *
     * @param \App\Models\UserAuth\Permission $permission
     * @return bool
     */
    public function hasPermissionThroughSet($permission): bool
    {
        return $this->permissionSets()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('id', $permission->id);
        })->exists();
    }
}
