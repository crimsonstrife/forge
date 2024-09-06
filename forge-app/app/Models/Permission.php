<?php

namespace App\Models;

use App\Traits\HasRoles;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use App\Contracts\Permission as PermissionContract;
use Spatie\Permission\Guard;
use App\Models\PermissionRegistrar as PermissionRegistrar;
use Spatie\Permission\Traits\RefreshesPermissionCache;

/**
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Permission extends SpatiePermission implements PermissionContract
{
    use HasRoles;
    use RefreshesPermissionCache;

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
        $this->table = config('permission.table_names.permissions') ?: parent::getTable();
    }

    /**
     * Summary of create
     * @return PermissionContract|Permission
     *
     * @throws PermissionAlreadyExists
     */
    public static function create(array $attributes = []): PermissionContract|Permission
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

        $permission = static::getPermission(['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']]);

        if ($permission) {
            throw PermissionAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        $createdPermission = static::query()->create($attributes);

        // Ensure the created permission is of the expected type
        if (!$createdPermission instanceof PermissionContract || !$createdPermission instanceof Permission) {
            throw new \Exception("The created permission is not of the expected type.");
        }

        return $createdPermission;
    }

    /**
     * A permission can be applied to roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions'),
            app(PermissionRegistrar::class)->pivotPermission,
            app(PermissionRegistrar::class)->pivotRole
        );
    }

    /**
     * A permission can be applied to permission sets.
     */
    public function permissionSets(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission_set'),
            config('permission.table_names.permission_set_permissions'),
            app(PermissionRegistrar::class)->pivotPermission,
            app(PermissionRegistrar::class)->pivotPermissionSet
        );
    }

    /**
     * A permission can be applied to permission set groups.
     */
    public function permissionSetGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission_set_group'),
            config('permission.table_names.permission_set_group_permissions'),
            app(PermissionRegistrar::class)->pivotPermission,
            app(PermissionRegistrar::class)->pivotPermissionSetGroup
        );
    }

    /**
     * A permission belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name'] ?? config('auth.defaults.guard')),
            'model',
            config('permission.table_names.model_has_permissions'),
            app(PermissionRegistrar::class)->pivotPermission,
            config('permission.column_names.model_morph_key')
        );
    }

    /**
     * Find a permission by its name (and optionally guardName).
     *
     * @return PermissionContract|Permission
     *
     * @throws PermissionDoesNotExist
     */
    public static function findByName(string $name, ?string $guardName = null): PermissionContract|Permission
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['name' => $name, 'guard_name' => $guardName]);
        if (!$permission) {
            throw PermissionDoesNotExist::create($name, $guardName);
        }

        return $permission;
    }

    /**
     * Find a permission by its id (and optionally guardName).
     *
     * @return PermissionContract|Permission
     *
     * @throws PermissionDoesNotExist
     */
    public static function findById(int|string $id, ?string $guardName = null): PermissionContract|Permission
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission([(new static())->getKeyName() => $id, 'guard_name' => $guardName]);

        if (!$permission) {
            throw PermissionDoesNotExist::withId($id, $guardName);
        }

        return $permission;
    }

    /**
     * Find or create permission by its name (and optionally guardName).
     *
     * @return PermissionContract|Permission
     */
    public static function findOrCreate(string $name, ?string $guardName = null): PermissionContract|Permission
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['name' => $name, 'guard_name' => $guardName]);

        if (!$permission) {
            $createdPermission = static::query()->create(['name' => $name, 'guard_name' => $guardName]);

            // Ensure the created permission is of the expected type
            if (!$createdPermission instanceof PermissionContract || !$createdPermission instanceof Permission) {
                throw new \Exception("The created permission is not of the expected type.");
            }

            return $createdPermission;
        }

        return $permission;
    }

    /**
     * Get the current cached permissions.
     */
    protected static function getPermissions(array $params = [], bool $onlyOne = false): Collection
    {
        return app(PermissionRegistrar::class)
            ->setPermissionClass(static::class)
            ->getPermissions($params, $onlyOne);
    }

    /**
     * Get the current cached first permission.
     *
     * @return PermissionContract|Permission|null
     */
    protected static function getPermission(array $params = []): ?PermissionContract
    {
        /** @var PermissionContract|null */
        return static::getPermissions($params, true)->first();
    }
}
