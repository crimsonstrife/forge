<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Widgets\AssigneeWorkloadTable;
use App\Filament\Widgets\CumulativeFlowChart;
use App\Filament\Widgets\ProjectHealthStats;
use App\Filament\Widgets\ThroughputTrend;
use App\Models\Project;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ProjectAnalytics extends Page
{
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::OutlinedChartBar;
    protected static string|null|\UnitEnum $navigationGroup = 'Reports';
    protected static ?string $navigationLabel = 'Project Analytics';
    protected string $view = 'filament.pages.reports.project-analytics';

    public ?string $projectId = null;

    public static function canAccess(): bool
    {
        $u = auth()->user();
        return $u?->can('view reports') ?? false;
    }

    public function mount(): void
    {
        $this->projectId ??= Project::query()->orderBy('name')->value('id');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('projectId')
                ->label('Project')
                ->options(Project::query()->orderBy('name')->pluck('name', 'id')->all())
                ->reactive(),
        ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [ ProjectHealthStats::class ];
    }

    protected function getFooterWidgets(): array
    {
        return [ CumulativeFlowChart::class, ThroughputTrend::class, AssigneeWorkloadTable::class ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 3;
    }

    public function getWidgetData(): array
    {
        return [
            ProjectHealthStats::class => ['projectId' => $this->projectId],
            CumulativeFlowChart::class => ['projectId' => $this->projectId],
            ThroughputTrend::class => ['projectId' => $this->projectId],
            AssigneeWorkloadTable::class => ['projectId' => $this->projectId],
        ];
    }
}
