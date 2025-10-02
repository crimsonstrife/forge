<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $issue_id
 * @property int|null $from_status_id
 * @property int $to_status_id
 * @property string|null $changed_by_id
 * @property Carbon $changed_at
 */
class IssueStatusEvent extends Model
{
    use HasUuids;

    protected $table = 'issue_status_events';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'issue_id',
        'from_status_id',
        'to_status_id',
        'changed_by_id',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }
}
