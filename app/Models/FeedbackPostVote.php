<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackPostVote extends Model
{
    use HasFactory;
    use HasUlids;

    protected $guarded = [];

    /** @return BelongsTo<FeedbackPost,FeedbackPostVote> */
    public function post(): BelongsTo
    {
        return $this->belongsTo(FeedbackPost::class, 'post_id');
    }

    /** @return BelongsTo<FeedbackIdentity,FeedbackPostVote> */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(FeedbackIdentity::class, 'identity_id');
    }
}
