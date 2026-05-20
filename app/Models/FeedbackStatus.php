<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedbackStatus extends Model
{
    use HasFactory;
    use HasUlids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_terminal' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    /** @return BelongsTo<FeedbackBoard,FeedbackStatus> */
    public function board(): BelongsTo
    {
        return $this->belongsTo(FeedbackBoard::class, 'board_id');
    }

    /** @return HasMany<FeedbackPost> */
    public function posts(): HasMany
    {
        return $this->hasMany(FeedbackPost::class, 'status_id');
    }
}
