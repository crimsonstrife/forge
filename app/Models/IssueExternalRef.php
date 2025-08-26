<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class IssueExternalRef extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'issue_id',
        'provider',
        'external_id',
        'read_only',
        'meta',
    ];
    protected $casts = [
        'issue_id' => 'string',
        'read_only' => 'bool',
        'meta' => 'array'
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function issue(): BelongsTo {
        return $this->belongsTo(Issue::class);
    }
}
