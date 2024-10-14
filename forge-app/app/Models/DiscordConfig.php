<?php

namespace App\Models;

use App\Traits\IsPermissable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscordConfig extends Model
{
    use HasFactory;
    use IsPermissable;

    // Specify the table name
    protected $table = 'discord_config';

    protected $fillable = [
        'enabled',
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

    public static function count()
    {
        return self::getDiscordConfig() ? 1 : 0;
    }

    // Encrypt client_secret and bot_token automatically when saving to the database
    public function setClientSecretAttribute($value)
    {
        $this->attributes['client_secret'] = encrypt($value);
    }

    public function setBotTokenAttribute($value)
    {
        $this->attributes['bot_token'] = encrypt($value);
    }

    // Decrypt client_secret and bot_token automatically when retrieving from the database
    public function getClientSecretAttribute($value)
    {
        return decrypt($value);
    }

    public function getBotTokenAttribute($value)
    {
        return decrypt($value);
    }
}
