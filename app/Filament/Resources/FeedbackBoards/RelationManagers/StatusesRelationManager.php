<?php

namespace App\Filament\Resources\FeedbackBoards\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StatusesRelationManager extends RelationManager
{
    protected static string $relationship = 'statuses';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('slug')->required(),
            Forms\Components\TextInput::make('color')->required(),
            Forms\Components\Toggle::make('is_default'),
            Forms\Components\Toggle::make('is_terminal'),
            Forms\Components\TextInput::make('position')->numeric()->default(0),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('slug')->badge(),
                Tables\Columns\IconColumn::make('is_default')->boolean(),
                Tables\Columns\IconColumn::make('is_terminal')->boolean(),
                Tables\Columns\TextColumn::make('position')->numeric(),
            ])
            ->headerActions([Actions\CreateAction::make()])
            ->recordActions([Actions\EditAction::make(), Actions\DeleteAction::make()]);
    }
}
