<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Exception;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

final class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $title = 'Members';

    /**
     * @throws Exception
     */
    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('pivot.role')
                ->options(['Owner' => 'Owner','Maintainer' => 'Maintainer','Contributor' => 'Contributor'])
                ->required(),
        ]);
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('pivot.role')->badge(),
            ])
            ->headerActions([Actions\AttachAction::make()->preloadRecordSelect()])
            ->recordActions([Actions\DetachAction::make()->requiresConfirmation()]);
    }
}
