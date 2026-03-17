<?php

namespace App\Filament\Resources\Milestones\Schemas;

use App\Enums\MilestoneState;
use App\Enums\MilestoneType;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MilestoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Milestone')
                    ->columns(2)
                    ->schema([
                        Select::make('project_id')
                            ->label('Project')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->required()
                            ->options(MilestoneType::class)
                            ->native(false),

                        TextInput::make('version')
                            ->maxLength(50)
                            ->helperText('Optional. Required for Release milestones.')
                            ->required(fn (callable $get): bool => static::isReleaseType($get('type'))),

                        Select::make('state')
                            ->required()
                            ->options(MilestoneState::class)
                            ->native(false),

                        DatePicker::make('starts_at')
                            ->label('Start date'),

                        DatePicker::make('due_at')
                            ->label('Due date'),

                        DatePicker::make('released_at')
                            ->label('Release date')
                            ->helperText('Optional. Typically used for Release milestones.'),
                    ]),
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
