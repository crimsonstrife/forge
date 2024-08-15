<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * Class LatestProjects
 *
 * This class represents a widget that displays the latest projects.
 * It extends the BaseWidget class.
 */
class LatestProjects extends BaseWidget
{
    protected static ?int $sort = 7;
    protected int|string|array $columnSpan = [
        'sm' => 1,
        'md' => 6,
        'lg' => 3
    ];

    /**
     * Retrieves the heading for the Latest Projects widget.
     *
     * @return string The heading for the LatestProjects widget.
     */
    public function getHeading(): string
    {
        return __('Latest projects');
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
     * Check if table pagination is enabled.
     *
     * @return bool Returns true if table pagination is enabled, false otherwise.
     */
    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    /**
     * Retrieve the query builder instance for the table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTableQuery(): Builder
    {
        return Project::query()
            ->limit(5)
            ->where(function ($query) {
                return $query->where('owner_id', Auth::user()->id)
                    ->orWhereHas('users', function ($query) {
                        return $query->where('users.id', Auth::user()->id);
                    });
            })
            ->latest();
    }

    /**
     * Retrieve the table columns for the LatestProjects widget.
     *
     * @return array The table columns.
     */
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('Project name'))
                ->formatStateUsing(fn($record) => new HtmlString('
                            <div class="w-full flex items-center gap-2">
                                <div style=\'background-image: url("' . $record->cover . '")\'
                                 class="w-8 h-8 bg-cover bg-center bg-no-repeat"></div>
                                ' . $record->name . '
                            </div>
                        ')),

            TextColumn::make('owner.name')
                ->label(__('Project owner')),

            TextColumn::make('status.name')
                ->label(__('Project status'))
                ->formatStateUsing(fn($record) => new HtmlString('
                            <div class="flex items-center gap-2">
                                <span class="filament-tables-color-column relative flex h-6 w-6 rounded-md"
                                    style="background-color: ' . $record->status->color . '"></span>
                                <span>' . $record->status->name . '</span>
                            </div>
                        ')),
        ];
    }
}
