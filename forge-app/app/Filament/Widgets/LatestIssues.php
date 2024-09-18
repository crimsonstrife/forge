<?php

namespace App\Filament\Widgets;

use App\Models\Issues\Issue;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * Class LatestIssues
 *
 * This class represents a widget that displays the latest issues.
 * It extends the BaseWidget class.
 */
class LatestIssues extends BaseWidget
{
    protected static ?int $sort = 6;
    protected int|string|array $columnSpan = [
        'sm' => 1,
        'md' => 6,
        'lg' => 3
    ];

    /**
     * Retrieves the heading for the LatestIssues widget.
     *
     * @return string The heading for the LatestIssues widget.
     */
    public function getHeading(): string
    {
        return __('Latest issues');
    }

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
     * Determines if table pagination is enabled.
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
        return Issue::query()
            ->limit(5)
            ->where(function ($query) {
                return $query->where('owner_id', Auth::user()->id)
                    ->orWhere('responsible_id', Auth::user()->id)
                    ->orWhereHas('project', function ($query) {
                        return $query->where('owner_id', Auth::user()->id)
                            ->orWhereHas('users', function ($query) {
                                return $query->where('users.id', Auth::user()->id);
                            });
                    });
            })
            ->latest();
    }

    /**
     * Retrieve the table columns for the LatestIssues widget.
     *
     * @return array The array of table columns.
     */
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('Issue'))
                ->formatStateUsing(fn ($record) => new HtmlString('
                    <div class="flex flex-col gap-1">
                        <span class="text-gray-400 font-medium text-xs">
                            ' . $record->project->name . '
                        </span>
                        <span>
                            <a href="' . route('filament.resources.issues.share', $record->code)
                    . '" target="_blank" class="text-primary-500 text-sm hover:underline">'
                    . $record->code
                    . '</a>
                            <span class="text-sm text-gray-400">|</span> '
                    . $record->name . '
                        </span>
                        ' . ($record->responsible ? '
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1 text-xs text-gray-400">'
                        . view('components.user-avatar', ['user' => $record->responsible])
                        . '<span>' . $record->responsible?->name . '</span>'
                        . '</div>
                        </div>' : '') . '
                    </div>
                ')),

            TextColumn::make('status.name')
                ->label(__('Status'))
                ->formatStateUsing(fn ($record) => new HtmlString('
                            <div class="flex items-center gap-2 mt-1">
                                <span class="filament-tables-color-column relative flex h-6 w-6 rounded-md"
                                    style="background-color: ' . $record->status->color . '"
                                    title="' . $record->status->name . '"></span>
                            </div>
                        ')),

            TextColumn::make('type.name')
                ->label(__('Type'))
                ->formatStateUsing(fn ($record) => view('components.issue-type', ['type' => $record->type])),

            TextColumn::make('priority.name')
                ->label(__('Priority'))
                ->formatStateUsing(fn ($record) => new HtmlString('
                            <div class="flex items-center gap-2 mt-1">
                                <span class="filament-tables-color-column relative flex h-6 w-6 rounded-md"
                                    style="background-color: ' . $record->priority->color . '"
                                    title="' . $record->priority->name . '"></span>
                            </div>
                        ')),
        ];
    }
}
