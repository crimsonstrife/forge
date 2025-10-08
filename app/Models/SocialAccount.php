<?php

namespace App\Models;

use App\Casts\EncryptedOrPlainToken;
use App\Utilities\TokenUtils;
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
        'token' => EncryptedOrPlainToken::class,
        'refresh_token' => EncryptedOrPlainToken::class,
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

        if (TokenUtils::isMaskedToken($v)) {
            return;
        }

        $this->attributes['token'] = $v;
    }

    public function setRefreshTokenAttribute(?string $value): void
    {
        $this->attributes['refresh_token'] = $value ? trim($value) : null;
    }
}
