<?php

namespace App\Filament\Resources\FeedbackIdentities;

use App\Filament\Resources\FeedbackIdentities\Pages\EditFeedbackIdentity;
use App\Filament\Resources\FeedbackIdentities\Pages\ListFeedbackIdentities;
use App\Models\FeedbackIdentity;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class FeedbackIdentityResource extends Resource
{
    protected static ?string $model = FeedbackIdentity::class;

    protected static string|null|\UnitEnum $navigationGroup = 'Support';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identity')->schema([
                Forms\Components\TextInput::make('display_name')->required()->maxLength(80),
                Forms\Components\TextInput::make('avatar_url')->url(),
                Forms\Components\DateTimePicker::make('email_verified_at')->disabled(),
                Forms\Components\DateTimePicker::make('blocked_at')->disabled(),
                Forms\Components\TextInput::make('blocked_reason')->maxLength(255),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_name')->searchable(),
                Tables\Columns\IconColumn::make('email_verified_at')->label('Verified')->boolean(fn ($state) => $state !== null),
                Tables\Columns\IconColumn::make('blocked_at')->label('Blocked')->boolean(fn ($state) => $state !== null),
                Tables\Columns\TextColumn::make('posts_count')->counts('posts')->label('Posts'),
                Tables\Columns\TextColumn::make('comments_count')->counts('comments')->label('Comments'),
                Tables\Columns\TextColumn::make('last_seen_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Actions\Action::make('blockToggle')
                    ->label(fn (FeedbackIdentity $record) => $record->blocked_at ? 'Unblock' : 'Block')
                    ->form([
                        Forms\Components\TextInput::make('reason')->maxLength(255),
                    ])
                    ->action(function (FeedbackIdentity $record, array $data): void {
                        if ($record->blocked_at) {
                            $record->forceFill([
                                'blocked_at' => null,
                                'blocked_reason' => null,
                                'blocked_by_user_id' => null,
                            ])->save();

                            return;
                        }

                        $record->forceFill([
                            'blocked_at' => now(),
                            'blocked_reason' => $data['reason'] ?? null,
                            'blocked_by_user_id' => auth()->id(),
                        ])->save();
                    }),
                Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFeedbackIdentities::route('/'),
            'edit' => EditFeedbackIdentity::route('/{record}/edit'),
        ];
    }
}
