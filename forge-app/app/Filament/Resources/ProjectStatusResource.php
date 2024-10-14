<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectStatusResource\Pages;
use App\Filament\Resources\ProjectStatusResource\RelationManagers;
use App\Models\Projects\ProjectStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectStatusResource extends Resource
{
    protected static ?string $model = ProjectStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Status Name'),

                Forms\Components\ColorPicker::make('color')
                    ->required()
                    ->label('Status Color'),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->nullable(),

                Forms\Components\Toggle::make('is_default')
                    ->label('Set as Default')
                    ->reactive()
                    ->helperText('If selected, this status will be the default for new projects globally.')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        // Ensure only one status is set as the global default
                        if ($state) {
                            ProjectStatus::where('id', '<>', $get('id'))
                                ->update(['is_default' => false]);
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Status Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ColorColumn::make('color')
                    ->label('Color'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description'),

                // Using IconColumn for the "is_default" field
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->sortable(),
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
            'index' => Pages\ListProjectStatuses::route('/'),
            'create' => Pages\CreateProjectStatus::route('/create'),
            'edit' => Pages\EditProjectStatus::route('/{record}/edit'),
        ];
    }
}
