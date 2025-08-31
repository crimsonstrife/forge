<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $issue_id
 * @property string $user_id
 * @property Carbon $started_at
 * @property Carbon|null $ended_at
 * @property int $duration_seconds
 * @property string $source
 * @property string|null $notes
 */
final class TimeEntry extends Model
{
    use HasUuids;

    /** @var array<int, string> */
    protected $fillable = [
        'issue_id',
        'user_id',
        'started_at',
        'ended_at',
        'duration_seconds',
        'source',
        'notes',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Current elapsed seconds for this entry (live if not ended).
     */
    public function elapsedSeconds(): Attribute
    {
        return Attribute::get(function (): int {
            if ($this->ended_at !== null) {
                return $this->duration_seconds;
            }

            return now()->diffInSeconds($this->started_at);
        });
    }

    /**
     * Finalize this entry by setting ended_at and locking duration_seconds.
     */
    public function finalizeNow(): void
    {
        if ($this->ended_at !== null) {
            return;
        }

        $this->ended_at = now();

        // Clamp to avoid any negative due to skew
        $this->duration_seconds = max(
            0,
            $this->ended_at->getTimestamp() - $this->started_at->getTimestamp()
        );

        $this->save();
    }

}
