<?php

namespace App\Models;

use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Organization extends BaseModel
{
    use HasFactory;
    use HasSlug;
    use HasUuids;
    use IsPermissible;
    use SoftDeletes;

    protected $table = 'organizations';

    protected $keyType = 'string';

    public $incrementing = false;

    /** @var array<string, string> */
    protected $casts = ['id' => 'string'];

    /** @var array<int, string> */
    protected $fillable = ['name', 'slug'];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function goals(): MorphMany
    {
        return $this->morphMany(Goal::class, 'owner');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isAccessibleBy(User $user): bool
    {
        if ($user->hasPermissionTo('is-super-admin') || $user->can('is-admin')) {
            return true;
        }

        return $this->projects()
            ->visibleTo($user)
            ->exists();
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasPermissionTo('is-super-admin') || $user->can('is-admin')) {
            return $query;
        }

        return $query->whereHas('projects', fn (Builder $projects) => $projects->visibleTo($user));
    }

    protected static function booted(): void
    {
        static::creating(static function (Organization $org): void {
            if (empty($org->slug)) {
                $org->slug = self::makeUniqueSlug($org->name);
            }
        });

        // Keep existing slugs stable. Only fill if it's empty on legacy rows.
        static::updating(static function (Organization $org): void {
            if (empty($org->slug)) {
                $org->slug = self::makeUniqueSlug($org->name, $org->getKey());
            }
        });
    }

    /**
     * Generate a unique, URL-safe slug from a name.
     */
    public static function makeUniqueSlug(string $name, ?string $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;

        $query = static::query();
        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        while ($query->clone()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->usingSeparator('-')
            ->doNotGenerateSlugsOnUpdate();
    }
}
