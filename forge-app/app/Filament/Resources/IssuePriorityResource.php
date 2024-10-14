<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IssuePriorityResource\Pages;
use App\Filament\Resources\IssuePriorityResource\RelationManagers;
use App\Models\Issues\IssuePriority;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IssuePriorityResource extends Resource
{
    protected static ?string $model = IssuePriority::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Priority Name'),

                Forms\Components\ColorPicker::make('color')
                    ->required()
                    ->label('Priority Color'),

                Forms\Components\TextInput::make('icon')
                    ->required()
                    ->label('Priority Icon (CSS class or URL)'),

                Forms\Components\Toggle::make('is_default')
                    ->label('Set as Default')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Priority Name'),
                Tables\Columns\TextColumn::make('color')->label('Priority Color'),
                Tables\Columns\IconColumn::make('is_default')->label('Default Priority')->boolean(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIssuePriorities::route('/'),
            'create' => Pages\CreateIssuePriority::route('/create'),
            'edit' => Pages\EditIssuePriority::route('/{record}/edit'),
        ];
    }
}
