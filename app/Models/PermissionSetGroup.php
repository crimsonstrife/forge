<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class PermissionSetGroup extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name', 'description', 'is_system'];
    protected $casts = ['is_system' => 'bool',
        'id' => 'string'];

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    /** @return BelongsToMany<PermissionSet> */
    public function permissionSets(): BelongsToMany
    {
        return $this->belongsToMany(
            PermissionSet::class,
            'group_permission_sets'
        );
    }
}
