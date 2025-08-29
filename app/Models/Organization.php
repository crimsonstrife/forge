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

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
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
