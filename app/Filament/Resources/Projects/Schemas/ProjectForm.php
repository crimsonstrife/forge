<?php

namespace App\Filament\Resources\Projects\Schemas;

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
                        Forms\Components\TextInput::make('name')->required()->maxLength(120),
                        Forms\Components\TextInput::make('key')->unique(ignoreRecord: true)->required()->regex('/^[A-Z0-9]{2,10}$/')->helperText('2–10 uppercase A–Z/0–9'),
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
