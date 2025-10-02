<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Widgets\AssigneeWorkloadTable;
use App\Filament\Widgets\CumulativeFlowChart;
use App\Filament\Widgets\ProjectHealthStats;
use App\Filament\Widgets\SprintBurndown;
use App\Filament\Widgets\ThroughputTrend;
use App\Models\Project;
use DB;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class ProjectAnalytics extends Page
{
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::OutlinedChartBar;
    protected static string|null|\UnitEnum $navigationGroup = 'Reports';
    protected static ?string $navigationLabel = 'Project Analytics';
    protected string $view = 'filament.pages.reports.project-analytics';

    public ?string $projectId = null;
    public ?string $dateFrom = null; // Y-m-d
    public ?string $dateTo   = null; // Y-m-d

    public static function canAccess(): bool
    {
        $u = auth()->user();
        return $u?->can('view.reports') ?? false;
    }

    public function mount(): void
    {
        $this->projectId ??= session('reports.projectId')
            ?? Project::query()->orderBy('name')->value('id');

        $this->dateFrom ??= session('reports.dateFrom') ?? now()->subDays(29)->toDateString();
        $this->dateTo   ??= session('reports.dateTo')   ?? now()->toDateString();
    }

    /** Persist selections so the page “remembers” */
    public function updated(string $name, mixed $value): void
    {
        if ($name === 'projectId') {
            session(['reports.projectId' => $value]);
        } elseif ($name === 'dateFrom') {
            session(['reports.dateFrom' => $value]);
        } elseif ($name === 'dateTo') {
            session(['reports.dateTo' => $value]);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('projectId')
                ->label('Project')
                ->options(Project::query()->orderBy('name')->pluck('name', 'id')->all())
                ->reactive(),

            DatePicker::make('dateFrom')->label('From')->maxDate(fn () => $this->dateTo)->reactive(),
            DatePicker::make('dateTo')->label('To')->minDate(fn () => $this->dateFrom)->reactive(),
        ]);
    }

    /** Export buttons */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportThroughputCsv')
                ->label('Export Throughput CSV')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->visible(fn (): bool => filled($this->projectId))
                ->url(
                    fn () => filled($this->projectId)
                    ? route('reports.throughput.csv', ['project' => (string) $this->projectId, 'from' => $this->dateFrom, 'to' => $this->dateTo])
                    : null
                )
                ->disabled(fn () => blank($this->projectId))
                ->action(function (): void {
                    $this->redirectRoute('reports.throughput.csv', [
                        'project' => (string) $this->projectId,
                        'from'    => $this->dateFrom,
                        'to'      => $this->dateTo,
                    ]);
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [ ProjectHealthStats::class ];
    }

    protected function getFooterWidgets(): array
    {
        return [ CumulativeFlowChart::class, ThroughputTrend::class, AssigneeWorkloadTable::class, SprintBurndown::class, ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 3;
    }

    public function getWidgetData(): array
    {
        return [
            ProjectHealthStats::class    => ['projectId' => $this->projectId, 'dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo],
            CumulativeFlowChart::class   => ['projectId' => $this->projectId, 'dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo],
            ThroughputTrend::class       => ['projectId' => $this->projectId, 'dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo],
            AssigneeWorkloadTable::class => ['projectId' => $this->projectId],
            SprintBurndown::class        => ['projectId' => $this->projectId],
        ];
    }

    public function hasReportData(): bool
    {
        return DB::table('report_project_daily_summaries')
            ->where('project_id', $this->projectId)
            ->when($this->dateFrom, fn ($q) => $q->whereDate('report_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('report_date', '<=', $this->dateTo))
            ->exists();
    }
}
