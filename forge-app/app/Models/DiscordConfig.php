<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscordConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'instance_id',
        'client_id',
        'client_secret',
        'bot_token',
        'redirect_uri',
        'role_mappings',
        'channel_mappings'
    ];

    protected $casts = [
        'role_mappings' => 'array',
        'channel_mappings' => 'array',
    ];
}
