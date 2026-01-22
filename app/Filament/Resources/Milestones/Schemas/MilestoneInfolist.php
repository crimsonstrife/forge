<?php

namespace App\Filament\Resources\Milestones\Schemas;

use App\Models\Milestone;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MilestoneInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Overview')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('project.name')
                            ->label('Project'),

                        TextEntry::make('name'),

                        TextEntry::make('type')
                            ->badge(),

                        TextEntry::make('state')
                            ->badge(),

                        TextEntry::make('version')
                            ->placeholder('—'),

                        TextEntry::make('due_at')
                            ->label('Due')
                            ->date('M j, Y')
                            ->placeholder('—'),

                        TextEntry::make('issues_count')
                            ->label('Issues')
                            ->state(function (Milestone $record): int {
                                return $record->issues()->count();
                            }),

                        TextEntry::make('sprints_count')
                            ->label('Sprints')
                            ->state(function (Milestone $record): int {
                                return $record->sprints()->count();
                            }),
                    ]),
            ]);
    }
}
