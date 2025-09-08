<?php

namespace App\Models;

use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Repository extends BaseModel
{
    use HasUuids;
    use IsPermissible;

    protected $fillable = [
        'provider',
        'host',
        'owner',
        'name',
        'external_id',
        'default_branch',
        'meta',
    ];

    protected $keyType = 'string';
    public $incrementing = false;
    protected $casts = [
        'meta' => 'array',
        'id' => 'string',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    /** @return HasMany<ProjectRepository> */
    public function projectLinks(): HasMany
    {
        return $this->hasMany(ProjectRepository::class);
    }

    /** @return HasMany<IssueExternalRef> */
    public function externalIssues(): HasMany
    {
        return $this->hasMany(IssueExternalRef::class);
    }

    /** @return HasMany<IssueStatusMapping> */
    public function statusMappings(): HasMany
    {
        return $this->hasMany(IssueStatusMapping::class);
    }
}
