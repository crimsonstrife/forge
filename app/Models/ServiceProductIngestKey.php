<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property-read string $id
 * @property string $service_product_id
 * @property string|null $name
 * @property string $secret_hash
 * @property string|null $created_by
 * @property CarbonImmutable|null $last_used_at
 * @property CarbonImmutable|null $revoked_at
 */
class ServiceProductIngestKey extends Model
{
    use HasUlids;

    /** @var array<int,string> */
    protected $fillable = ['service_product_id', 'name', 'secret_hash', 'created_by', 'last_used_at', 'revoked_at'];

    protected $casts = [
        'last_used_at' => 'immutable_datetime',
        'revoked_at'   => 'immutable_datetime',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::ulid();
        });
    }

    public function serviceProduct(): BelongsTo
    {
        return $this->belongsTo(ServiceProduct::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }
}
