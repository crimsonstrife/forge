<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueNotificationPreference extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'notify_on_assignment',
        'notify_on_comment',
        'notify_on_status_change',
        'notify_on_link_change',
        'notify_on_mention',
        'daily_digest_enabled',
        'daily_digest_last_sent_at',
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'notify_on_assignment' => 'bool',
        'notify_on_comment' => 'bool',
        'notify_on_status_change' => 'bool',
        'notify_on_link_change' => 'bool',
        'notify_on_mention' => 'bool',
        'daily_digest_enabled' => 'bool',
        'daily_digest_last_sent_at' => 'immutable_datetime',
    ];

    protected $attributes = [
        'notify_on_assignment' => true,
        'notify_on_comment' => true,
        'notify_on_status_change' => true,
        'notify_on_link_change' => true,
        'notify_on_mention' => true,
        'daily_digest_enabled' => false,
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enabledFor(string $event): bool
    {
        return match ($event) {
            'assignment' => (bool) $this->notify_on_assignment,
            'comment' => (bool) $this->notify_on_comment,
            'status_change' => (bool) $this->notify_on_status_change,
            'link_change' => (bool) $this->notify_on_link_change,
            'mention' => (bool) $this->notify_on_mention,
            'digest' => (bool) $this->daily_digest_enabled,
            default => true,
        };
    }
}
