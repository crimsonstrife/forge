<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueFollower extends Model
{
    use HasUuids;

    protected $table = 'issue_followers';

    protected $fillable = [
        'issue_id',
        'user_id',
    ];

    protected $casts = [
        'id' => 'string',
        'issue_id' => 'string',
        'user_id' => 'string',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
