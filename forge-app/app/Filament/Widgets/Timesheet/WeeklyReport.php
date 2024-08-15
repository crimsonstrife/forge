<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Timesheet;

use App\Models\Timesheet;
use App\Models\User;
use App\Models\IssueHour;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\ChartWidget;

/**
 * Represents a WeeklyReport widget.
 *
 * This class extends the ChartWidget class and is used to display a weekly report.
 *
 * @package Filament\Widgets\Timesheet
 */
class WeeklyReport extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected int|string|array $columnSpan = [
        'sm' => 1,
        'md' => 6,
        'lg' => 3
    ];

    /**
     * WeeklyReport constructor.
     *
     * @param int|null $id The ID of the report (optional).
     */
    public function __construct($id = null)
    {
        $weekDaysData = $this->getWeekStartAndFinishDays();

        $this->filter = $weekDaysData['weekStartDate'] . ' - ' . $weekDaysData['weekEndDate'];

        parent::__construct($id);
    }

    /**
     * Retrieves the heading for the WeeklyReport widget.
     *
     * @return string The heading for the WeeklyReport widget.
     */
    public function getHeading(): string
    {
        return __('Weekly logged time');
    }

    /**
     * Retrieve the data for the WeeklyReport widget.
     *
     * @return array The data for the WeeklyReport widget.
     */
    protected function getData(): array
    {
        $weekDaysData = explode(' - ', $this->filter);
        $user = Auth::user();

        $collection = new Collection(
            $this->filter($user, [
            'year' => null,
            'weekStartDate' => $weekDaysData[0],
            'weekEndDate' => $weekDaysData[1]
            ])
        );

        $dates = $this->buildDatesRange($weekDaysData[0], $weekDaysData[1]);

        $datasets = $this->buildReport($collection, $dates);

        return [
            'datasets' => [
                [
                    'label' => __('Weekly time logged'),
                    'data' => $datasets,
                    'backgroundColor' => [
                        'rgba(54, 162, 235, .6)'
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, .8)'
                    ],
                ],
            ],
            'labels' => $dates,
        ];
    }

    /**
     * Retrieve the filters for the WeeklyReport widget.
     *
     * @return array|null The filters for the WeeklyReport widget.
     */
    protected function getFilters(): ?array
    {
        return $this->yearWeeks();
    }

    /**
     * Builds a report based on the given collection and dates.
     *
     * @param Collection $collection The collection to build the report from.
     * @param array $dates The array of dates to include in the report.
     * @return array The built report.
     */
    protected function buildReport(Collection $collection, array $dates): array
    {
        $template = $this->createReportTemplate($dates);
        foreach ($collection as $item) {
            $template[$item->day]['value'] =  $item->value;
        }
        return collect($template)->pluck('value')->toArray();
    }

    /**
     * Filter the weekly report for a specific user.
     *
     * @param User $user The user for whom to filter the report.
     * @param array $params Additional parameters for filtering the report.
     * @return void
     */
    protected function filter(User $user, array $params)
    {
        return IssueHour::select([
            DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d') as day"),
            DB::raw('SUM(value) as value'),
        ])
            ->whereBetween('created_at', [$params['weekStartDate'], $params['weekEndDate']])
            ->whereRaw(
                DB::raw("YEAR(created_at)=" . (is_null($params['year']) ? Carbon::now()->format('Y') : $params['year']))
            )
            ->where('user_id', $user->id)
            ->groupBy(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"))
            ->get();
    }

    /**
     * Builds the dates range for the weekly report.
     *
     * @param string $weekStartDate The start date of the week.
     * @param string $weekEndDate The end date of the week.
     * @return array The array containing the dates range.
     */
    protected function buildDatesRange($weekStartDate, $weekEndDate): array
    {
        $period = CarbonPeriod::create($weekStartDate, $weekEndDate);

        $dates = [];
        foreach ($period as $item) {
            $dates[] = $item->format('Y-m-d');
        }

        return $dates;
    }

    /**
     * Create a report template for the weekly timesheet report.
     *
     * @param array $dates The array of dates for the report.
     * @return array The created report template.
     */
    protected function createReportTemplate(array $dates): array
    {
        $template = [];
        foreach ($dates as $date) {
            $template[$date]['value'] = 0;
        }
        return $template;
    }

    /**
     * Retrieves an array of year weeks.
     *
     * @return array The array of year weeks.
     */
    protected function yearWeeks(): array
    {
        $year = date_create('today')->format('Y');

        $dtStart = date_create('2 jan ' . $year)->modify('last Monday');
        $dtEnd = date_create('last monday of Dec ' . $year);

        for ($weeks = []; $dtStart <= $dtEnd; $dtStart->modify('+1 week')) {
            $from = $dtStart->format('Y-m-d');
            $to = (clone $dtStart)->modify('+6 Days')->format('Y-m-d');
            $weeks[$from . ' - ' . $to] = $from . ' - ' . $to;
        }

        return $weeks;
    }

    /**
     * Retrieves the start and finish days of the week.
     *
     * @return array An array containing the start and finish days of the week.
     */
    protected function getWeekStartAndFinishDays(): array
    {
        $now = Carbon::now();

        return [
            'weekStartDate' => $now->startOfWeek()->format('Y-m-d'),
            'weekEndDate' => $now->endOfWeek()->format('Y-m-d')
        ];
    }

    /**
     * Get the type of the WeeklyReport.
     *
     * @return string The type of the WeeklyReport.
     */
    protected function getType(): string
    {
        return 'bar';
    }
}
