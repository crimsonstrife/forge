<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedbackCategory extends Model
{
    use HasFactory;
    use HasUlids;

    protected $guarded = [];

    /** @return BelongsTo<FeedbackBoard,FeedbackCategory> */
    public function board(): BelongsTo
    {
        return $this->belongsTo(FeedbackBoard::class, 'board_id');
    }

    /** @return HasMany<FeedbackPost> */
    public function posts(): HasMany
    {
        return $this->hasMany(FeedbackPost::class, 'category_id');
    }
}
