<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasFactory;
    use HasUuids;

    public $keyType = 'string';

    public $incrementing = false;

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
}
