<?php

namespace App\Models;

use App\Traits\HasRecordShares;
use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Repository extends BaseModel
{
    use HasRecordShares;
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

    public function supportsIssueSync(): bool
    {
        return in_array(strtolower((string) $this->provider), self::issueSyncProviders(), true);
    }

    /**
     * Providers that support importing issues into Forge.
     *
     * @return list<string>
     */
    public static function issueSyncProviders(): array
    {
        return ['github'];
    }

    public function supportsVcsLinks(): bool
    {
        return in_array(strtolower((string) $this->provider), ['github', 'crucible'], true);
    }

    public function supportsVcsCreation(): bool
    {
        return in_array(strtolower((string) $this->provider), ['github', 'crucible'], true);
    }

    public function displayPath(): string
    {
        if (strtolower((string) $this->provider) === 'crucible') {
            $org = (string) Arr::get($this->meta, 'organization_name', $this->owner);
            $repo = (string) Arr::get($this->meta, 'repository_name', $this->name);

            return trim($org.'/'.$repo, '/');
        }

        return trim($this->owner.'/'.$this->name, '/');
    }

    public function slugPath(): string
    {
        return trim($this->owner.'/'.$this->name, '/');
    }

    public function externalUrl(): ?string
    {
        return match (strtolower((string) $this->provider)) {
            'github' => 'https://'.($this->host ?: 'github.com').'/'.$this->slugPath(),
            'crucible' => Arr::get($this->meta, 'web_url'),
            default => null,
        };
    }
}
