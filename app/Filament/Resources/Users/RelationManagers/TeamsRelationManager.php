<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\Team;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class TeamsRelationManager extends RelationManager
{
    protected static string $relationship = 'teams';
    protected static ?string $title = 'Teams';

    public function form(Schema $schema): Schema
    {
        // Used by the "Edit Role" table action
        return $schema->schema([
            TextInput::make('role')->label('Role')->maxLength(50),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('pivot.role')->label('Role'),
                Tables\Columns\TextColumn::make('created_at')->since()->label('Team Created')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Actions\AttachAction::make()
                    ->label('Attach Team')
                    ->recordSelectOptionsQuery(fn () => Team::query()->orderBy('name'))
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                Actions\Action::make('editPivotRole')
                    ->label('Edit Role')
                    ->icon('heroicon-m-pencil-square')
                    ->form([TextInput::make('role')->label('Role')->maxLength(50)])
                    ->action(function ($record, array $data): void {
                        $this->getRelationship()->updateExistingPivot($record->getKey(), [
                            'role' => $data['role'] ?? null,
                        ]);
                    }),
                Actions\DetachAction::make(),
            ])
            ->emptyStateHeading('No teams')
            ->emptyStateDescription('Attach this user to one or more teams.');
    }
}
