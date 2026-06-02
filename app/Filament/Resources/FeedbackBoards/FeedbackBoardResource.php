<?php

namespace App\Filament\Resources\FeedbackBoards;

use App\Filament\Resources\FeedbackBoards\Pages\CreateFeedbackBoard;
use App\Filament\Resources\FeedbackBoards\Pages\EditFeedbackBoard;
use App\Filament\Resources\FeedbackBoards\Pages\ListFeedbackBoards;
use App\Filament\Resources\FeedbackBoards\RelationManagers\CategoriesRelationManager;
use App\Filament\Resources\FeedbackBoards\RelationManagers\StatusesRelationManager;
use App\Models\FeedbackBoard;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class FeedbackBoardResource extends Resource
{
    protected static ?string $model = FeedbackBoard::class;

    protected static string|null|\UnitEnum $navigationGroup = 'Support';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Board')->schema([
                Forms\Components\Select::make('service_product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('slug')->required()->maxLength(255),
                Forms\Components\TextInput::make('public_url')->required()->url()->maxLength(255),
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                Forms\Components\Select::make('default_sort')->options([
                    'top' => 'Top',
                    'new' => 'New',
                    'trending' => 'Trending',
                ])->required(),
                Forms\Components\Toggle::make('is_public')->default(true),
                Forms\Components\Toggle::make('allow_anonymous_read')->default(true),
            ])->columns(2),
            Section::make('Mail')->schema([
                Forms\Components\TextInput::make('mail_from_address')->email()->maxLength(255),
                Forms\Components\TextInput::make('mail_from_name')->maxLength(255),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('slug')->badge(),
                Tables\Columns\TextColumn::make('product.name')->label('Product')->searchable(),
                Tables\Columns\IconColumn::make('is_public')->boolean(),
                Tables\Columns\TextColumn::make('posts_count')->counts('posts')->label('Posts'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CategoriesRelationManager::class,
            StatusesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFeedbackBoards::route('/'),
            'create' => CreateFeedbackBoard::route('/create'),
            'edit' => EditFeedbackBoard::route('/{record}/edit'),
        ];
    }
}
