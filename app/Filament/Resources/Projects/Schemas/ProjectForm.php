<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStage;
use Exception;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Project;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    /**
     * @throws Exception
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)->schema([
                    Section::make('Details')->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()->maxLength(120)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn ($state, Set $set) => $set('key', app(\App\Support\Keys\ProjectKeyGenerator::class)->suggest((string) $state, 3))
                            ),
                        Forms\Components\TextInput::make('key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->rule('alpha_num:ascii|min:2|max:10|unique:projects,key')
                            ->maxLength(10)
                            ->regex('/^[A-Z0-9]{2,10}$/')
                            ->helperText('2–10 uppercase A–Z/0–9')
                            ->formatStateUsing(fn ($state) => \Illuminate\Support\Str::upper($state)),
                        Forms\Components\Textarea::make('description')->rows(4)->columnSpanFull(),
                        Forms\Components\Select::make('stage')
                            ->options(collect(ProjectStage::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)])->all())
                            ->required(),
                        Forms\Components\Select::make('lead_id')
                            ->label('Project Lead')
                            ->searchable()
                            ->relationship('users', 'name') // optional; or preload User::query()
                            ->preload()
                            ->nullable(),
                    ])->columns(2),
                    Section::make('Public Tracker')->schema([
                        Forms\Components\Toggle::make('public_tracker_enabled')->label('Enable public tracker'),
                        Forms\Components\TextInput::make('public_slug')
                            ->helperText('Public URL slug (must be unique).')
                            ->unique(ignoreRecord: true)
                            ->visible(fn ($get) => $get('public_tracker_enabled') === true),
                        Forms\Components\Toggle::make('count_private_in_progress')
                            ->label('Include private issues in progress counters')
                            ->visible(fn ($get) => $get('public_tracker_enabled') === true),
                        Forms\Components\Repeater::make('embed_domains')
                            ->label('Allowed embed parent origins (frame-ancestors)')
                            ->schema([
                                Forms\Components\TextInput::make('origin')
                                    ->label('Origin')
                                    ->required()
                                    ->placeholder('https://example.com')
                            ])
                            ->addActionLabel('Add origin')
                            ->visible(fn ($get) => $get('public_tracker_enabled') === true)
                            ->columns(1)
                            ->formatStateUsing(function ($state) {
                                // Convert array of strings to array of objects with 'origin' key
                                if (is_array($state) && (count($state) === 0 || is_string(array_values($state)[0]))) {
                                    return collect($state)->map(fn ($item) => ['origin' => $item])->all();
                                }
                                return $state;
                            })
                            ->dehydrateStateUsing(function ($state) {
                                // Convert array of objects with 'origin' key to array of strings
                                if (is_array($state) && (count($state) === 0 || is_array(array_values($state)[0]))) {
                                    return collect($state)->pluck('origin')->filter()->values()->all();
                                }
                                return $state;
                            }),
                    ])->columns(2),
                ]),
                Grid::make(1)->schema([
                    Section::make('Dates')->schema([
                        Forms\Components\DatePicker::make('started_at')->native(false)->closeOnDateSelection(),
                        Forms\Components\DatePicker::make('due_at')->native(false)->closeOnDateSelection(),
                        Forms\Components\DatePicker::make('ended_at')->native(false)->closeOnDateSelection(),
                    ]),
                ]),

                Section::make('Settings')->collapsible()->collapsed()->schema([
                    Forms\Components\Textarea::make('settings')->json(true)->helperText('JSON settings (advanced)'),
                ])->columnSpanFull(),
            ])->statePath('data');
    }
}
