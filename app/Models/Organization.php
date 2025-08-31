<?php

namespace App\Models;

use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public function getRouteKeyName(): string
    {
        return 'slug';
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
