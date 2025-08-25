<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class PermissionSet extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name', 'description', 'is_system'];
    protected $casts = ['is_system' => 'bool'];

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    /** @return BelongsToMany<Permission> */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_set_permissions'
        );
    }

    /** @return BelongsToMany<Permission> */
    public function mutedPermissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_set_mutes'
        );
    }

    /** @return BelongsToMany<PermissionSetGroup> */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            PermissionSetGroup::class,
            'group_permission_sets'
        );
    }

    /** @return BelongsToMany<Role> */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_permission_sets',
            'permission_set_id',
            config('permission.column_names.role_pivot_key', 'role_id')
        );
    }
}
