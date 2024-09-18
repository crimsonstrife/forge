<?php

namespace App\Models\Issues;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Issues\Issue;

/**
 * IssueSubscriber Model
 *
 * Represents a subscriber to an issue in the application.
 *
 * @property int $id
 * @property int $user_id
 * @property int $issue_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Issues\Issue $issue
 */
class IssueSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'issue_id',
    ];

    /**
     * Get the user associated with the issue subscriber.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the issue associated with the issue subscriber.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'user_id', 'id');
    }
}
