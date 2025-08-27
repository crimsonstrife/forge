<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\ProjectStage;
use App\Models\Project;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';
    protected static ?string $title = 'Projects';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('role')->label('Role')->maxLength(50),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('key')->badge()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('pivot.role')->label('Role'),
                Tables\Columns\TextColumn::make('stage_label')
                    ->label('Stage')
                    ->badge()
                    ->placeholder('—')
                    ->color(fn (Project $r) => $r->stage instanceof ProjectStage ? $r->stage->color() : 'gray'),
            ])
            ->headerActions([
                Actions\AttachAction::make()
                    ->label('Attach Project')
                    ->recordSelectOptionsQuery(fn () => Project::query()->orderBy('name'))
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                Actions\Action::make('editPivotRole')
                    ->label('Edit Role')
                    ->icon('heroicon-m-pencil-square')
                    ->form([TextInput::make('role')->label('Role')->maxLength(50)])
                    ->action(function ($record, array $data): void {
                        $this->getRelationship()->updateExistingPivot($record->getKey(), [
                            'role' => $data['role'] ?? null,
                        ]);
                    }),
                Actions\DetachAction::make(),
            ])
            ->emptyStateHeading('No projects')
            ->emptyStateDescription('Attach this user to projects and set a role.');
    }
}
