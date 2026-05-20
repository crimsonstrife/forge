<?php

namespace App\Filament\Resources\ServiceProducts\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FeedbackBoardsRelationManager extends RelationManager
{
    protected static string $relationship = 'feedbackBoards';

    protected static ?string $title = 'Feedback Boards';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('slug')->required(),
            Forms\Components\TextInput::make('public_url')->required()->url(),
            Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
            Forms\Components\Toggle::make('is_public')->default(true),
            Forms\Components\Toggle::make('allow_anonymous_read')->default(true),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('slug')->badge(),
                Tables\Columns\IconColumn::make('is_public')->boolean(),
                Tables\Columns\TextColumn::make('posts_count')->counts('posts')->label('Posts'),
            ])
            ->headerActions([Actions\CreateAction::make()])
            ->recordActions([Actions\EditAction::make(), Actions\DeleteAction::make()]);
    }
}
