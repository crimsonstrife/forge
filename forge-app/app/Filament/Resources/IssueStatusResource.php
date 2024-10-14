<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IssueStatusResource\Pages;
use App\Filament\Resources\IssueStatusResource\RelationManagers;
use App\Models\Issues\IssueStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IssueStatusResource extends Resource
{
    protected static ?string $model = IssueStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

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

                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->required()
                    ->label('Order')
                    ->helperText('The order in which the statuses should appear'),

                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'name')
                    ->label('Project')
                    ->nullable()
                    ->placeholder('No Project (Global Status)') // Placeholder for the project select, also acts if the status is global (no project)
                    ->searchable(),

                Forms\Components\Toggle::make('is_default')
                    ->label('Set as Default')
                    ->reactive()
                    ->helperText('If selected, this status will be the default. If no project is selected, it will be the global default status.')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        // When "is_default" is toggled to true
                        if ($state) {
                            $project_id = $get('project_id');

                            // If the status is global (no project_id), update other global statuses
                            if ($project_id === null) {
                                IssueStatus::whereNull('project_id')
                                    ->where('id', '<>', $get('id'))
                                    ->update(['is_default' => false]);
                            } else {
                                // If it's project-specific, update other statuses for that project
                                IssueStatus::where('project_id', $project_id)
                                    ->where('id', '<>', $get('id'))
                                    ->update(['is_default' => false]);
                            }
                        }
                    }),
            ]);
    }

    // Define the table for listing IssueStatus records
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

            Tables\Columns\TextColumn::make('order')
                ->label('Order')
                ->sortable(),

            Tables\Columns\IconColumn::make('is_default')
                ->label('Default')
                ->boolean()
                ->sortable(),

            Tables\Columns\TextColumn::make('project.name')
                ->label('Project')
                ->placeholder('Global Status'), // Display "Global Status" if project_id is null
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
            'index' => Pages\ListIssueStatuses::route('/'),
            'create' => Pages\CreateIssueStatus::route('/create'),
            'edit' => Pages\EditIssueStatus::route('/{record}/edit'),
        ];
    }
}
