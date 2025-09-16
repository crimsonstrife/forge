<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Exception;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class IssuePrioritiesRelationManager extends RelationManager
{
    protected static string $relationship = 'issuePriorities';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Issue Priorities';

    /**
     * @throws Exception
     */
    public function form(Schema $schema): Schema
    {
        // Pivot: order, is_default
        return $schema->schema([
            Forms\Components\TextInput::make('pivot.order')
                ->numeric()->minValue(0)->label('Order')->default(0),
            Forms\Components\Toggle::make('pivot.is_default')->label('Default priority'),
        ])->columns(2);
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('key')->badge(),
                Tables\Columns\TextColumn::make('weight')->numeric(),
                Tables\Columns\ColorColumn::make('color'),
                Tables\Columns\TextColumn::make('pivot.order')->numeric(),
                Tables\Columns\IconColumn::make('pivot.is_default')->boolean(),
            ])
            ->headerActions([
                Actions\AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                Actions\EditAction::make(),   // edits pivot
                Actions\DetachAction::make()->requiresConfirmation(),
            ]);
    }
}
