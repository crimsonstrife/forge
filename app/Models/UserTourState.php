<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTourState extends Model
{
    use HasUuids;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_SNOOZED = 'snoozed';

    public const STATUS_DISMISSED = 'dismissed';

    public const STATUS_COMPLETED = 'completed';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'tour',
        'status',
        'last_step',
        'started_at',
        'snoozed_until',
        'dismissed_at',
        'completed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_step' => 'integer',
            'started_at' => 'datetime',
            'snoozed_until' => 'datetime',
            'dismissed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
