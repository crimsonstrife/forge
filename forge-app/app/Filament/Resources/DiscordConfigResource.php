<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscordConfigResource\Pages;
use App\Filament\Resources\DiscordConfigResource\RelationManagers;
use App\Models\DiscordConfig;
use App\Settings\DiscordSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiscordConfigResource extends Resource
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static string $settings = DiscordSettings::class;

    protected static ?string $slug = 'discord-settings';

    protected ?string $heading = 'Manage Discord Settings';

    protected ?string $subheading = 'Configure the Discord settings of the platform.';

    protected static ?string $navigationGroup = 'Modules';

    protected static ?string $navigationLabel = 'Discord';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('client_id')
                    ->label('Client ID')
                    ->required(),

                Forms\Components\TextInput::make('client_secret')
                    ->label('Client Secret')
                    ->password()  // Hide the value as a password field
                    ->dehydrated()  // Ensure it's saved as encrypted
                    ->required(),

                Forms\Components\TextInput::make('bot_token')
                    ->label('Bot Token')
                    ->password()  // Hide the value as a password field
                    ->dehydrated()  // Ensure it's saved as encrypted
                    ->required(),

                Forms\Components\TextInput::make('guild_id')
                    ->label('Guild ID (Server ID)')
                    ->required(),

                Forms\Components\TextInput::make('redirect_uri')
                    ->label('Redirect URI')
                    ->required(),

                Forms\Components\Textarea::make('role_mappings')
                    ->label('Role Mappings')
                    ->json()
                    ->rows(5),

                Forms\Components\Textarea::make('channel_mappings')
                    ->label('Channel Mappings')
                    ->json()
                    ->rows(5),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_id')->label('Client ID'),
                Tables\Columns\TextColumn::make('guild_id')->label('Guild ID'),
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscordConfig::route('/'),
            //'create' => Pages\CreateDiscordConfig::route('/create'),
            'edit' => Pages\EditDiscordConfig::route('/{record}/edit'),
        ];
    }
}
