<?php

namespace App\Models;

use App\Traits\IsPermissable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DiscordConfig
 *
 * This class represents the configuration settings for Discord integration.
 * It extends the base Model class provided by the framework.
 *
 * @package App\Models
 */
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


    /**
     * Get the role mappings attribute.
     *
     * @param mixed $value The value of the role mappings attribute.
     * @return mixed The processed role mappings attribute.
     */
    public function getRoleMappingsAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Set the role mappings attribute.
     *
     * @param mixed $value The value to set for the role mappings attribute.
     * @return void
     */
    public function setRoleMappingsAttribute($value)
    {
        $this->attributes['role_mappings'] = json_encode($value);
    }

    /**
     * Get the channel mappings attribute.
     *
     * @param mixed $value The value of the channel mappings attribute.
     * @return mixed The processed channel mappings attribute.
     */
    public function getChannelMappingsAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Set the channel mappings attribute.
     *
     * @param mixed $value The value to set for the channel mappings attribute.
     */
    public function setChannelMappingsAttribute($value)
    {
        $this->attributes['channel_mappings'] = json_encode($value);
    }

    /**
     * Retrieve the Discord configuration settings.
     *
     * @return array The Discord configuration settings.
     */
    public static function getDiscordConfig()
    {
        return self::first() ?: new self();
    }

    /**
     * Updates the Discord configuration with the provided data.
     *
     * @param array $data An associative array containing the configuration data to be updated.
     * @return void
     */
    public static function updateDiscordConfig($data)
    {
        $discordConfig = self::getDiscordConfig();
        if ($discordConfig) {
            // Verify that the discord configuration is an instance of the DiscordConfig model
            if ($discordConfig instanceof self) {
                $discordConfig->update($data);
            }
        } else {
            self::create($data);
        }
    }

    /**
     * Count the number of Discord configurations.
     *
     * @return int The count of Discord configurations.
     */
    public static function count()
    {
        return self::getDiscordConfig() ? 1 : 0;
    }


    /**
     * Set the client secret attribute.
     *
     * @param string $value The client secret value to be set.
     * @return void
     */
    public function setClientSecretAttribute($value)
    {
        $this->attributes['client_secret'] = encrypt($value);
    }

    /**
     * Set the bot token attribute.
     *
     * @param string $value The bot token value.
     * @return void
     */
    public function setBotTokenAttribute($value)
    {
        $this->attributes['bot_token'] = encrypt($value);
    }


    /**
     * Get the client secret attribute.
     *
     * @param string $value The value of the client secret attribute.
     * @return string The processed client secret attribute.
     */
    public function getClientSecretAttribute($value)
    {
        return decrypt($value);
    }

    /**
     * Get the bot token attribute.
     *
     * @param mixed $value The value of the bot token attribute.
     * @return mixed The processed bot token attribute.
     */
    public function getBotTokenAttribute($value)
    {
        return decrypt($value);
    }
}
