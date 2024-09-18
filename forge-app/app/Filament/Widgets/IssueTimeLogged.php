<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Issues\Issue;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\ChartWidget;

/**
 * This class represents a widget for displaying time logged on issues using a chart.
 */
class IssueTimeLogged extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '300px';
    protected int|string|array $columnSpan = [
        'sm' => 1,
        'md' => 6,
        'lg' => 3
    ];

    /**
     * Determine if the authenticated user can view the list of issues.
     *
     * @return bool Returns true if the user has the 'List Issues' permission, otherwise false.
     */
    public static function canView(): bool
    {
        // Get the authenticated user and check if they have the 'List Issues' permission.
        $user = Auth::user();
        $permission = 'List Issues';
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission);
        }
        return false;
    }

    /**
     * Retrieves the heading string for time logged by issues.
     *
     * @return string The localized heading text.
     */
    public function getHeading(): string
    {
        return __('Time logged by issues');
    }

    /**
     * Retrieves an array of data for charting purposes.
     *
     * The method queries the Issue model, filters the results to include only those
     * with hours logged, and limits the results to 10 entries. It then constructs
     * an array containing the datasets and labels needed for charting.
     *
     * @return array An associative array containing 'datasets' and 'labels' for charting.
     */
    protected function getData(): array
    {
        $query = Issue::query();
        $query->has('hours');
        $query->limit(10);
        return [
            'datasets' => [
                [
                    'label' => __('Total time logged (hours)'),
                    'data' => $query->get()->pluck('totalLoggedInHours')->toArray(),
                    'backgroundColor' => [
                        'rgba(54, 162, 235, .6)'
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, .8)'
                    ],
                ],
            ],
            'labels' => $query->get()->pluck('code')->toArray(),
        ];
    }

    /**
     * Retrieves the type of the object.
     *
     * @return string The type of the object.
     */
    protected function getType(): string
    {
        return 'bar';
    }
}
