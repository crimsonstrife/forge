<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $email_encrypted
 * @property string $email_hash
 * @property string $token
 */
class SupportIdentity extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'email_encrypted' => 'encrypted',
            'last_seen_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    /** @return HasMany<Ticket> */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'email_hash', 'email_hash');
    }
}
