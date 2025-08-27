<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectStage;
use Exception;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Project;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class ProjectsTable
{
    /**
     * @throws Exception
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->searchable()->badge(),
                Tables\Columns\TextColumn::make('name')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('stage')->formatStateUsing(fn($s)=>ucfirst($s?->value ?? ''))->badge(),
                Tables\Columns\TextColumn::make('started_at')->date(),
                Tables\Columns\TextColumn::make('due_at')->date(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault:true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stage')
                    ->options(collect(ProjectStage::cases())->mapWithKeys(fn($c)=>[$c->value=>ucfirst($c->value)])->all()),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
