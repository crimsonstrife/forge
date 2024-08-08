<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'issue_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'user_id', 'id');
    }
}
