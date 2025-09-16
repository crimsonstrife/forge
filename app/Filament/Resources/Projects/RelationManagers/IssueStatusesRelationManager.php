<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Exception;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class IssueStatusesRelationManager extends RelationManager
{
    protected static string $relationship = 'issueStatuses';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Issue Statuses';

    /**
     * @throws Exception
     */
    public function form(Schema $schema): Schema
    {
        // Pivot: order, is_initial, is_default_done
        return $schema->schema([
            Forms\Components\TextInput::make('pivot.order')
                ->numeric()->minValue(0)->label('Order')->default(0),
            Forms\Components\Toggle::make('pivot.is_initial')->label('Initial status'),
            Forms\Components\Toggle::make('pivot.is_default_done')->label('Default “done”'),
        ])->columns(3);
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
                Tables\Columns\ColorColumn::make('color'),
                Tables\Columns\IconColumn::make('is_done')->boolean()->label('Global Done'),
                Tables\Columns\TextColumn::make('pivot.order')->numeric(),
                Tables\Columns\IconColumn::make('pivot.is_initial')->boolean(),
                Tables\Columns\IconColumn::make('pivot.is_default_done')->boolean(),
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
