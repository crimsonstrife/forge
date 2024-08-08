<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id',
        'old_status_id',
        'new_status_id',
        'user_id'
    ];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id', 'id')->withTrashed();
    }

    public function oldStatus(): BelongsTo
    {
        return $this->belongsTo(IssueStatus::class, 'old_status_id', 'id')->withTrashed();
    }

    public function newStatus(): BelongsTo
    {
        return $this->belongsTo(IssueStatus::class, 'new_status_id', 'id')->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
