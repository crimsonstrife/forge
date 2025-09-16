<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Exception;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StatusTransitionsRelationManager extends RelationManager
{
    protected static string $relationship = 'statusTransitions';

    protected static ?string $title = 'Status Transitions';

    /**
     * @throws Exception
     */
    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('from_status_id')
                ->label('From')
                ->options(fn () => $this->getOwnerRecord()
                    ->issueStatuses()
                    ->pluck('issue_statuses.name', 'issue_statuses.id'))
                ->required()
                ->searchable(),

            Forms\Components\Select::make('to_status_id')
                ->label('To')
                ->options(fn () => $this->getOwnerRecord()
                    ->issueStatuses()
                    ->pluck('issue_statuses.name', 'issue_statuses.id'))
                ->required()
                ->searchable(),

            Forms\Components\Select::make('issue_type_id')
                ->label('Only for Issue Type')
                ->options(fn () => $this->getOwnerRecord()
                    ->issueTypes()
                    ->pluck('issue_types.name', 'issue_types.id'))
                ->searchable()
                ->nullable(),

            Forms\Components\Toggle::make('is_global')
                ->label('Apply to all types when not scoped')
                ->default(true),
        ])->columns(2);
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('from.name')->label('From'),
                Tables\Columns\TextColumn::make('to.name')->label('To'),
                Tables\Columns\TextColumn::make('type.name')->label('Type')->placeholder('All'),
                Tables\Columns\IconColumn::make('is_global')->boolean()->label('Global'),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make()->requiresConfirmation(),
            ]);
    }
}
