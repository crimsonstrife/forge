<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscordConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'guild_id',
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

    public function getRoleMappingsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setRoleMappingsAttribute($value)
    {
        $this->attributes['role_mappings'] = json_encode($value);
    }

    public function getChannelMappingsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setChannelMappingsAttribute($value)
    {
        $this->attributes['channel_mappings'] = json_encode($value);
    }

    public static function getDiscordConfig()
    {
        return self::first();
    }

    public static function updateDiscordConfig($data)
    {
        $discordConfig = self::getDiscordConfig();
        if ($discordConfig) {
            $discordConfig->update($data);
        } else {
            self::create($data);
        }
    }
}
