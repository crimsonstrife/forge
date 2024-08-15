<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

/**
 * FavoriteProjects class.
 *
 * This class represents a widget for displaying favorite projects.
 * It extends the BaseWidget class.
 */
class FavoriteProjects extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 6,
        'lg' => 6
    ];

    /**
     * Retrieve the number of columns for the FavoriteProjects widget.
     *
     * @return int The number of columns.
     */
    protected function getColumns(): int
    {
        return 4;
    }

    /**
     * Determine if the authenticated user can view the list of projects.
     *
     * @return bool Returns true if the user has the 'List Projects' permission, otherwise false.
     */
    public static function canView(): bool
    {
        // Get the authenticated user and check if they have the 'List Projects' permission.
        $user = Auth::user();
        $permission = 'List Projects';
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission);
        }
        return false;
    }

    /**
     * Retrieves the cards for the FavoriteProjects widget.
     *
     * @return array The array of cards.
     */
    protected function getCards(): array
    {
        $user = Auth::user();
        $favoriteProjects = $user->favoriteProjects;
        $cards = [];
        foreach ($favoriteProjects as $project) {
            $issuesCount = $project->issues()->count();
            $contributorsCount = $project->contributors->count();
            $cards[] = Stat::make('', new HtmlString('
                    <div class="flex items-center gap-2 -mt-2 text-lg">
                        <div style=\'background-image: url("' . $project->cover . '")\'
                             class="w-8 h-8 bg-cover bg-center bg-no-repeat"></div>
                        <span>' . $project->name . '</span>
                    </div>
                '))
                ->color('success')
                ->extraAttributes([
                    'class' => 'hover:shadow-lg'
                ])
                ->description(new HtmlString('
                        <div class="w-full flex items-center gap-2 mt-2 text-gray-500 font-normal">'
                            . $issuesCount
                            . ' '
                            . __($issuesCount > 1 ? 'Issues' : 'Issue')
                            . ' '
                            . __('and')
                            . ' '
                            . $contributorsCount
                            . ' '
                            . __($contributorsCount > 1 ? 'Contributors' : 'Contributor')
                        . '</div>
                        <div class="text-xs w-full flex items-center gap-2 mt-2">
                            <a class="text-primary-400 hover:text-primary-500 hover:cursor-pointer"
                               href="' . route('filament.resources.projects.view', $project) . '">
                                ' . __('View details') . '
                            </a>
                            <span class="text-gray-300">|</span>
                            <a class="text-primary-400 hover:text-primary-500 hover:cursor-pointer"
                               href="' . route('filament.pages.kanban/{project}', ['project' => $project->id]) . '">
                                ' . __('Issues') . '
                            </a>
                        </div>
                    '));
        }
        return $cards;
    }
}
