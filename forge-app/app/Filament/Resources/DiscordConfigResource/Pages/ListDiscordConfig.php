<?php

namespace App\Filament\Resources\DiscordConfigResource\Pages;

use App\Filament\Resources\DiscordConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Models\DiscordConfig;
use App\Settings\DiscordSettings;
use App\Settings\ModuleSettings;

/**
 * Class ListDiscordConfig
 *
 * This class extends the ListRecords class and is used to manage the listing of Discord configuration records
 * within the Filament resource.
 *
 * @package App\Filament\Resources\DiscordConfigResource\Pages
 */
class ListDiscordConfig extends ListRecords
{
    protected static string $resource = DiscordConfigResource::class;

    /**
     * Retrieves the header actions for the Discord configuration resource.
     *
     * @return array An array of header actions.
     */
    protected function getHeaderActions(): array
    {
        // Check if the Discord module is enabled from the ModuleSettings
        $isEnabled = app(ModuleSettings::class)->discord_enabled;

        return [
            // Create a new Discord Config, only if one doesn't already exist. There should only be one Discord Config in the database.
            Actions\CreateAction::make()
                ->label('Create Discord Config')
                ->modal('create-discord-config')
                ->icon('heroicon-o-plus-circle')
                ->button()
                ->createAnother(false)
                ->visible(fn () => DiscordConfig::count() === 0 && $isEnabled),
        ];
    }
}
