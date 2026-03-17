<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Enums\MilestoneState;
use App\Enums\MilestoneType;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    protected static ?string $title = 'Milestones';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTabComponent(Model $ownerRecord, string $pageClass): Tab
    {
        return Tab::make('Milestones')
            ->badge($ownerRecord->milestones()->count())
            ->badgeColor('info')
            ->icon('heroicon-m-flag');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('type')
                    ->required()
                    ->options(MilestoneType::class)
                    ->native(false)
                    ->live(),

                Forms\Components\TextInput::make('version')
                    ->maxLength(50)
                    ->visible(fn (callable $get): bool => static::isReleaseType($get('type')))
                    ->helperText('Optional. Typically used for Release milestones.'),

                Forms\Components\Select::make('state')
                    ->required()
                    ->options(MilestoneState::class)
                    ->native(false),

                Forms\Components\DatePicker::make('starts_at')
                    ->label('Start')
                    ->nullable(),

                Forms\Components\DatePicker::make('due_at')
                    ->label('Due')
                    ->nullable(),

                Forms\Components\DatePicker::make('released_at')
                    ->label('Released')
                    ->nullable()
                    ->visible(fn (callable $get): bool => static::isReleaseType($get('type'))),

                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull()
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(static fn (mixed $state): string => static::formatEnumState($state)),

                Tables\Columns\TextColumn::make('state')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(static fn (mixed $state): string => static::formatEnumState($state)),

                Tables\Columns\TextColumn::make('version')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('due_at')
                    ->label('Due')
                    ->date('M j, Y')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('issues_count')
                    ->label('Issues')
                    ->counts('issues')
                    ->alignEnd()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sprints_count')
                    ->label('Sprints')
                    ->counts('sprints')
                    ->alignEnd()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(MilestoneType::class),

                SelectFilter::make('state')
                    ->options(MilestoneState::class),
            ])
            ->defaultSort('due_at')
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    private static function isReleaseType(mixed $type): bool
    {
        if ($type instanceof MilestoneType) {
            return $type === MilestoneType::Release;
        }

        if ($type instanceof BackedEnum) {
            return $type->value === MilestoneType::Release->value;
        }

        return (string) $type === MilestoneType::Release->value || (string) $type === 'release';
    }

    private static function formatEnumState(mixed $state): string
    {
        if ($state instanceof BackedEnum) {
            return Str::headline((string) $state->value);
        }

        return Str::headline((string) $state);
    }
}
