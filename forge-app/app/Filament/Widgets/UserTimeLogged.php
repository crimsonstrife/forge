<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Issue;
use Filament\Widgets\ChartWidget;

/**
 * A widget for displaying the time logged by users in a chart format.
 */
class UserTimeLogged extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?int $sort = 5;
    protected static ?string $maxHeight = '300px';
    protected int|string|array $columnSpan = [
        'sm' => 1,
        'md' => 6,
        'lg' => 3
    ];

    /**
     * Determine if the authenticated user can view the list of tickets.
     *
     * @return bool Returns true if the user has the 'List tickets' permission, otherwise false.
     */
    public static function canView(): bool
    {
        return auth()->user()->can('List tickets');
    }

    /**
     * Retrieves the heading for the time logged by users.
     *
     * @return string The heading text.
     */
    public function getHeading(): string
    {
        return __('Time logged by users');
    }

    /**
     * Retrieves data for user hours and names.
     *
     * This method queries the User model to get users who have logged hours,
     * limits the result to 10 users, and formats the data for use in a dataset.
     *
     * @return array An associative array containing datasets and labels.
     */
    protected function getData(): array
    {
        $query = User::query();
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
            'labels' => $query->get()->pluck('name')->toArray(),
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
