<?php

namespace App\Models;

use App\Traits\HasRecordShares;
use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Repository extends BaseModel
{
    use HasUuids;
    use IsPermissible;
    use HasRecordShares;

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

    public function supportsIssueSync(): bool
    {
        return strtolower((string) $this->provider) === 'github';
    }

    public function displayPath(): string
    {
        if (strtolower((string) $this->provider) === 'crucible') {
            $org = (string) Arr::get($this->meta, 'organization_name', $this->owner);
            $repo = (string) Arr::get($this->meta, 'repository_name', $this->name);

            return trim($org . '/' . $repo, '/');
        }

        return trim($this->owner . '/' . $this->name, '/');
    }

    public function slugPath(): string
    {
        return trim($this->owner . '/' . $this->name, '/');
    }

    public function externalUrl(): ?string
    {
        return match (strtolower((string) $this->provider)) {
            'github' => 'https://' . ($this->host ?: 'github.com') . '/' . $this->slugPath(),
            'crucible' => Arr::get($this->meta, 'web_url'),
            default => null,
        };
    }
}
