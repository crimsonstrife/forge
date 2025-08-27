<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Password;

final class UsersTable
{
    /**
     * @throws Exception
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo_url')
                    ->label('')
                    ->circular()
                    ->size(36)
                    ->extraImgAttributes(['alt' => 'Avatar'])
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('email')
                    ->icon(fn (User $r) => $r->hasVerifiedEmail() ? 'heroicon-m-check-badge' : 'heroicon-m-x-mark')
                    ->iconColor(fn (User $r) => $r->hasVerifiedEmail() ? 'success' : 'warning')
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('teams_count')
                    ->counts('teams')
                    ->label('Teams')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('verified')
                    ->label('Verified email')
                    ->queries(
                        true:  fn ($q) => $q->whereNotNull('email_verified_at'),
                        false: fn ($q) => $q->whereNull('email_verified_at'),
                        blank: fn ($q) => $q
                    ),

                Tables\Filters\SelectFilter::make('team')
                    ->relationship('teams', 'name')
                    ->label('Team')
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('sendReset')
                    ->label('Send reset link')
                    ->icon('heroicon-m-envelope')
                    ->requiresConfirmation()
                    ->action(fn (User $record) => Password::sendResetLink(['email' => $record->email]))
                    ->successNotificationTitle('Password reset email queued'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
