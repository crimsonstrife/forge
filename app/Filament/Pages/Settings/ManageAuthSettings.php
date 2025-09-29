<?php

namespace App\Filament\Pages\Settings;

use App\Settings\AuthSettings;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageAuthSettings extends SettingsPage
{
    protected static string $settings = AuthSettings::class;

    protected static ?string $title = 'Authentication';
    protected static string|null|\UnitEnum $navigationGroup = 'Settings';
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Toggle::make('allowRegistration')
                ->label('Allow self-registration')
                ->helperText('When off, only admins can create accounts.'),
        ]);
    }
}
