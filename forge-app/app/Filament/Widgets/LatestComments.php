<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Issues\Issue;
use App\Models\Comment;
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
 * Class LatestComments
 *
 * This class represents a widget that displays the latest comments.
 * It extends the BaseWidget class.
 *
 * @package Filament\Widgets
 */
class LatestComments extends BaseWidget
{
    protected static ?int $sort = 8;
    protected int|string|array $columnSpan = [
        'sm' => 1,
        'md' => 6,
        'lg' => 3
    ];

    /**
     * Retrieves the heading for the LatestComments widget.
     *
     * @return string The heading for the LatestComments widget.
     */
    public function getHeading(): string
    {
        return __('Latest issue comments');
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
     * @return bool
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
        return Comment::query()
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
     * Retrieve the table columns for the LatestComments widget.
     *
     * @return array The table columns.
     */
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('issue')
                ->label(__('Issue'))
                ->formatStateUsing(function ($state) {
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
                    </div>
                ');
                }),

            TextColumn::make('user.name')
                ->label(__('Owner'))
                ->formatStateUsing(fn ($record) => view('components.user-avatar', ['user' => $record->user])),

            TextColumn::make('created_at')
                ->label(__('Commented at'))
                ->dateTime()
        ];
    }

    /**
     * Retrieve the table actions for the LatestComments widget.
     *
     * @return array The table actions.
     */
    protected function getTableActions(): array
    {
        return [
            Action::make('view')
                ->label(__('View'))
                ->icon('fas fa-eye')
                ->color('gray')
                ->modalHeading(__('Comment details'))
                ->modalSubmitActionLabel(__('View issue'))
                ->form([
                    RichEditor::make('content')
                        ->label(__('Content'))
                        ->default(fn ($record) => $record->content)
                        ->disabled()
                ])
                ->action(
                    fn ($record) =>
                        redirect()->to(route('filament.resources.issues.share', $record->issue->code))
                )
        ];
    }
}
