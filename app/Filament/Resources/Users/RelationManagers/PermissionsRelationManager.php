<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\Permission;
use App\Models\Team;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PermissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'permissions';
    protected static ?string $title = 'Direct Permissions';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('team_id')
                ->label('Team (scope)')
                ->options(Team::query()->orderBy('name')->pluck('name', 'id')->toArray())
                ->searchable()
                ->preload()
                ->native(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->badge()->searchable(),
                Tables\Columns\TextColumn::make('pivot.team_id')
                    ->label('Team Scope')
                    ->formatStateUsing(fn ($teamId) => $teamId
                        ? (Team::query()->find($teamId)?->name ?? '(deleted team)')
                        : 'Global'),
            ])
            ->headerActions([
                Actions\AttachAction::make()
                    ->label('Grant Permission')
                    ->recordSelectOptionsQuery(fn () => Permission::query()->orderBy('name'))
                    ->preloadRecordSelect()
                    ->schema([
                        Select::make('team_id')
                            ->label('Team (scope)')
                            ->options(Team::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->native(false),
                    ])
                    ->using(function (BelongsToMany $relationship, array $data): void {
                        // Attach with pivot team_id on model_has_permissions
                        if ($id = $data['recordId'] ?? null) {
                            $relationship->attach([$id => ['team_id' => $data['team_id'] ?? null]]);
                        }
                    }),
            ])
            ->recordActions([
                Actions\DetachAction::make()->label('Revoke'),
            ])
            ->emptyStateHeading('No direct permissions')
            ->emptyStateDescription('Attach permissions directly (bypassing roles).');
    }
}
