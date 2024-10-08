<?php

namespace App\Filament\Widgets;

use App\Models\Issues\IssuePriority;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Filament\Widgets\ChartWidget;

/**
 * Represents a widget that displays issues by priority in a chart.
 *
 * @package Filament\Widgets
 */
class IssuesByPriority extends ChartWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Chart';
    protected static ?string $maxHeight = '300px';
    protected int|string|array $columnSpan = [
        'sm' => 1,
        'md' => 6,
        'lg' => 3
    ];

    /**
     * Determine if the authenticated user can view the list of issues.
     *
     * @return bool Returns true if the user has the 'list-issue' permission, otherwise false.
     */
    public static function canView(): bool
    {
        // Get the authenticated user and check if they have the 'list-issue' permission.
        $user = Auth::user();
        $permission = 'list-issue';
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission);
        }
        return false;
    }

    /**
     * Retrieves the heading for the IssuesByPriority widget.
     *
     * @return string The heading for the widget.
     */
    public function getHeading(): string
    {
        return __('Issues by priorities');
    }

    /**
     * Retrieve the data for the IssuesByPriority widget.
     *
     * @return array The data for the widget.
     */
    protected function getData(): array
    {
        $data = IssuePriority::withCount('issues')->get();
        return [
            'datasets' => [
                [
                    'label' => __('Issues by priorities'),
                    'data' => $data->pluck('issues_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, .6)',
                        'rgba(54, 162, 235, .6)',
                        'rgba(255, 205, 86, .6)'
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, .8)',
                        'rgba(54, 162, 235, .8)',
                        'rgba(255, 205, 86, .8)'
                    ],
                    'hoverOffset' => 4
                ]
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    /**
     * Get the type of the widget.
     *
     * @return string The type of the widget.
     */
    protected function getType(): string
    {
        return 'doughnut';
    }
}
