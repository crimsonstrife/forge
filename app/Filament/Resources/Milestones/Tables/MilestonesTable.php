<?php

namespace App\Filament\Resources\Milestones\Tables;

use App\Enums\MilestoneState;
use App\Enums\MilestoneType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MilestonesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                return $query->with('project');
            })
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('project.name')
                    ->label('Project')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('state')
                    ->badge()
                    ->sortable(),

                TextColumn::make('due_at')
                    ->label('Due')
                    ->date('M j, Y')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('issues_count')
                    ->label('Issues')
                    ->counts('issues')
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('sprints_count')
                    ->label('Sprints')
                    ->counts('sprints')
                    ->alignEnd()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('project_id')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->options(MilestoneType::class),

                SelectFilter::make('state')
                    ->options(MilestoneState::class),
            ])
            ->defaultSort('due_at')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
