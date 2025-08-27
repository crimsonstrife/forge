<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\Role;
use Filament\Actions;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RolesRelationManager extends RelationManager
{
    /** Relation on App\Models\User from Spatie HasRoles trait */
    protected static string $relationship = 'roles';

    protected static ?string $title = 'Roles';

    public function form(Schema $schema): Schema
    {
        // No extra pivot fields for global roles
        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Role')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->label('Created')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Actions\AttachAction::make()
                    ->label('Grant Role')
                    ->recordSelectOptionsQuery(fn () => Role::query()->orderBy('name'))
                    ->preloadRecordSelect()
                    // Force a GLOBAL (non-team-scoped) grant:
                    ->using(function (BelongsToMany $relationship, array $data): void {
                        if ($id = $data['recordId'] ?? null) {
                            // If your `model_has_roles` has a `team_id` column, passing an empty array
                            // will default it to NULL (global). No extra pivot fields needed.
                            $relationship->attach([$id => []]);
                        }
                    }),
            ])
            ->recordActions([
                // Table action namespace:
                Actions\DetachAction::make()->label('Revoke'),
            ])
            ->emptyStateHeading('No roles')
            ->emptyStateDescription('Grant one or more global roles to this user.');
    }
}
