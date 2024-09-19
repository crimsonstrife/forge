<?php

namespace App\Models\Issues;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Issues\IssueStatus;
use App\Models\User;

/**
 * Class IssueActivity
 *
 * This class represents the IssueActivity model, which is responsible for storing and retrieving issue activity data.
 *
 * @package App\Models\Issues\Issues
 */
class IssueActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id',
        'old_status_id',
        'new_status_id',
        'user_id'
    ];

    /**
     * Get the issue associated with the issue activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id', 'id')->withTrashed();
    }

    /**
     * Get the old status of the issue activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oldStatus(): BelongsTo
    {
        return $this->belongsTo(IssueStatus::class, 'old_status_id', 'id')->withTrashed();
    }

    /**
     * Get the new status of the issue activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function newStatus(): BelongsTo
    {
        return $this->belongsTo(IssueStatus::class, 'new_status_id', 'id')->withTrashed();
    }

    /**
     * Get the user associated with the issue activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
