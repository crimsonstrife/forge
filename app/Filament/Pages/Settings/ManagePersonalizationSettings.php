<?php

namespace App\Filament\Pages\Settings;

use App\Settings\AuthSettings;
use App\Settings\PersonalizationSettings;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManagePersonalizationSettings extends SettingsPage
{
    protected static string $settings = PersonalizationSettings::class;

    protected static ?string $title = 'Personalization Settings';
    protected static string|null|\UnitEnum $navigationGroup = 'Settings';
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Toggle::make('solo_mode_default')
                ->label('Enable Solo-Developer Mode')
                ->helperText('When on, enables and prioritizes features to help Solo developers.'),
            Toggle::make('streamer_mode')
                ->label('Enable Streamer Mode')
                ->helperText('When on, enables features useful for OBS or streaming embeds'),
        ]);
    }
}
