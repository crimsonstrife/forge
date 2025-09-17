<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $email_encrypted
 * @property string $email_hash
 * @property string $token
 */
class SupportIdentity extends Model
{
    use HasUlids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'email_encrypted' => 'encrypted',
            'last_seen_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::ulid();
        });
    }

    /** @return HasMany<Ticket> */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'email_hash', 'email_hash');
    }
}
