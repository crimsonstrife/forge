<?php

namespace App\Models;

use App\Traits\HasPermissionSets;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Traits\HasPermissions;

class Role extends SpatieRole
{
    use HasUuids;
    use HasPermissionSets;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $casts = ['id' => 'string'];

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    /** @return BelongsToMany */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            config('permission.column_names.role_pivot_key', 'role_id'),
            config('permission.column_names.permission_pivot_key', 'permission_id')
        );
    }
}
