<?php

namespace App\Models;

use App\Enums\AccessLevel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $id
 * @property string $shareable_type
 * @property string $shareable_id
 * @property string $principal_type
 * @property string $principal_id
 * @property AccessLevel $access_level
 * @property bool $propagate_to_children
 * @property CarbonImmutable|null $expires_at
 */
class RecordShare extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    /** @var array<int, string> */
    protected $fillable = [
        'shareable_type', 'shareable_id',
        'principal_type', 'principal_id',
        'access_level', 'propagate_to_children', 'expires_at', 'grantor_id',
    ];

    /** @return array<string, string> */
    public function casts(): array
    {
        return [
            'access_level' => AccessLevel::class,
            'expires_at' => 'immutable_datetime',
            'propagate_to_children' => 'boolean',
        ];
    }

    public function shareable(): MorphTo
    {
        return $this->morphTo();
    }

    public function principal(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeNotExpired($q)
    {
        return $q->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }
}
