<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackCommentVote extends Model
{
    use HasFactory;
    use HasUlids;

    protected $guarded = [];

    /** @return BelongsTo<FeedbackComment,FeedbackCommentVote> */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(FeedbackComment::class, 'comment_id');
    }

    /** @return BelongsTo<FeedbackIdentity,FeedbackCommentVote> */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(FeedbackIdentity::class, 'identity_id');
    }
}
