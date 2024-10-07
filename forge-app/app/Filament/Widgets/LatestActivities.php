<?php

namespace App\Filament\Widgets;

use App\Models\Projects\Project;
use App\Models\Issues\Issue;
use App\Models\Comment;
use App\Models\Issues\IssueActivity;
use App\Models\User;
use Closure;
use Filament\Forms\Components\RichEditor;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Class LatestActivities
 *
 * This class represents a widget that displays the latest activities.
 * It extends the BaseWidget class.
 */
class LatestActivities extends BaseWidget
{
    protected static ?int $sort = 9;
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
        return __('Latest issue activities');
    }

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
        return IssueActivity::query()
            ->limit(5)
            ->whereHas('issue', function ($query) {
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
     * Retrieve the table columns for the LatestActivities widget.
     *
     * @return array The array of table columns.
     */
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('issue')
                ->label(__('Issue'))
                ->formatStateUsing(function ($record, $state) {
                    return new HtmlString('
                    <div class="flex flex-col gap-1">
                        <span class="text-gray-400 font-medium text-xs">
                            ' . $state->project->name . '
                        </span>
                        <span>
                            <a href="' . route('filament.resources.issues.share', $state->code)
                        . '" target="_blank" class="text-primary-500 text-sm hover:underline">'
                        . $state->code
                        . '</a>
                            <span class="text-sm text-gray-400">|</span> '
                        . $state->name . '
                        </span>
                        <div class="w-full flex items-center gap-2 text-sm">
                            <span style="color: ' . $record->oldStatus->color . '">'
                                . $record->oldStatus->name
                            . '</span>
                            <span class="text-gray-500">' . __('To') . '</span>
                            <span style="color: ' . $record->newStatus->color . '">
                                ' . $record->newStatus->name . '
                            </span>
                        </div>
                    </div>
                ');
                }),

            TextColumn::make('user.name')
                ->label(__('Changed by'))
                ->formatStateUsing(fn ($record) => view('components.user-avatar', ['user' => $record->user])),

            TextColumn::make('created_at')
                ->label(__('Performed at'))
                ->dateTime()
        ];
    }
}
