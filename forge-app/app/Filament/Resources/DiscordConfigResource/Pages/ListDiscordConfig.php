<?php

namespace App\Filament\Resources\DiscordConfigResource\Pages;

use App\Filament\Resources\DiscordConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Models\DiscordConfig;
use App\Settings\DiscordSettings;
use App\Settings\ModuleSettings;

class ListDiscordConfig extends ListRecords
{
    protected static string $resource = DiscordConfigResource::class;

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
                ->visible(fn () => DiscordConfig::count() === 0 && $isEnabled),
        ];
    }
}
