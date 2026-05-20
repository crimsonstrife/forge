<?php

namespace App\Models;

use Database\Seeders\FeedbackDefaultsSeeder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackBoard extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'allow_anonymous_read' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::created(static function (self $board): void {
            app(FeedbackDefaultsSeeder::class)->seed($board);
        });
    }

    /** @return BelongsTo<ServiceProduct,FeedbackBoard> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ServiceProduct::class, 'service_product_id');
    }

    /** @return BelongsTo<ServiceProduct,FeedbackBoard> */
    public function serviceProduct(): BelongsTo
    {
        return $this->product();
    }

    /** @return HasMany<FeedbackPost> */
    public function posts(): HasMany
    {
        return $this->hasMany(FeedbackPost::class, 'board_id');
    }

    /** @return HasMany<FeedbackCategory> */
    public function categories(): HasMany
    {
        return $this->hasMany(FeedbackCategory::class, 'board_id')->orderBy('position');
    }

    /** @return HasMany<FeedbackStatus> */
    public function statuses(): HasMany
    {
        return $this->hasMany(FeedbackStatus::class, 'board_id')->orderBy('position');
    }

    public function defaultStatus(): ?FeedbackStatus
    {
        return $this->statuses()->where('is_default', true)->first();
    }
}
