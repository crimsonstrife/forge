<?php

namespace App\Filament\Resources\OAuthClients;

use App\Filament\Resources\OAuthClients\Pages\CreateOAuthClient;
use App\Filament\Resources\OAuthClients\Pages\ListOAuthClients;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Laravel\Passport\Client;

class OAuthClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|null|\UnitEnum $navigationGroup = 'Access/Permissions';
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;
    protected static ?string $navigationLabel = 'OAuth Clients';
    protected static ?string $modelLabel = 'OAuth Client';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Client Name')
                ->placeholder('e.g. Codex (production)')
                ->required()
                ->maxLength(255),

            TextInput::make('redirect')
                ->label('Redirect URI')
                ->placeholder('https://codex.example.com/auth/forge/callback')
                ->helperText('The OAuth callback URL for the connected application.')
                ->url()
                ->required(),

            Toggle::make('revoked')
                ->label('Revoked')
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Client ID')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('redirect')
                    ->label('Redirect URI')
                    ->wrap(),

                IconColumn::make('revoked')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedXCircle)
                    ->falseIcon(Heroicon::OutlinedCheckCircle)
                    ->trueColor('danger')
                    ->falseColor('success'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('rotate_secret')
                    ->label('Rotate Secret')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->requiresConfirmation()
                    ->action(function (Client $record) {
                        $secret = Str::random(40);
                        $record->secret = $secret;
                        $record->save();
                        // Secret is shown via notification so admin can copy it
                        \Filament\Notifications\Notification::make()
                            ->title('New Secret Generated')
                            ->body("New client secret: {$secret}\n\nCopy it now — it will not be shown again.")
                            ->warning()
                            ->persistent()
                            ->send();
                    }),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOAuthClients::route('/'),
            'create' => CreateOAuthClient::route('/create'),
        ];
    }
}
