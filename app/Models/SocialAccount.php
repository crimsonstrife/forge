<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'provider_user_id',
        'nickname',
        'token',
        'refresh_token',
        'expires_at',
    ];

    protected $casts = [
        'token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setTokenAttribute(?string $value): void
    {
        if ($value === null) {
            $this->attributes['token'] = null;
            return;
        }

        $v = trim($value);

        if ($v === '' || preg_match('/^\*+$/', $v) === 1 || str_starts_with($v, '***')) {
            return;
        }

        $this->attributes['token'] = $v;
    }

    public function setRefreshTokenAttribute(?string $value): void
    {
        $this->attributes['refresh_token'] = $value ? trim($value) : null;
    }
}
