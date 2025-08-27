<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Exception;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class IssueTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'issueTypes';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Issue Types';

    /**
     * @throws Exception
     */
    public function form(Schema $schema): Schema
    {
        // Pivot: order, is_default
        return $schema->schema([
            Forms\Components\TextInput::make('pivot.order')
                ->numeric()->minValue(0)->label('Order')->default(0),
            Forms\Components\Toggle::make('pivot.is_default')->label('Default for project'),
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
                Tables\Columns\IconColumn::make('pivot.is_default')->boolean(),
                Tables\Columns\TextColumn::make('pivot.order')->numeric(),
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
