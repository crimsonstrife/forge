<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackSession extends Model
{
    use HasFactory;
    use HasUlids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'expires_at' => 'immutable_datetime',
            'last_seen_at' => 'immutable_datetime',
            'revoked_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<FeedbackIdentity,FeedbackSession> */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(FeedbackIdentity::class, 'identity_id');
    }
}
