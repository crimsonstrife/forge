<?php

namespace App\Models;

use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasFactory;
    use HasUuids;
    use IsPermissible;

    public $keyType = 'string';
    public $incrementing = false;
    protected $casts = [
        'id' => 'string',
        'abilities'   => 'array',
        'last_used_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function tokenable(): MorphTo
    {
        return parent::tokenable();
    }
}
