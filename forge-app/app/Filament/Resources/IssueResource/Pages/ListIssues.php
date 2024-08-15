<?php

namespace App\Filament\Resources\IssueResource\Pages;

use App\Filament\Resources\IssueResource;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ListIssues
 *
 * This class represents a list issues page.
 */
class ListIssues extends ListRecords
{
    protected static string $resource = IssueResource::class;

    /**
     * Determines whether the table filters should be persisted in the session.
     *
     * @return bool True if the table filters should be persisted in the session, false otherwise.
     */
    protected function shouldPersistTableFiltersInSession(): bool
    {
        return true;
    }

    /**
     * Retrieves the actions for the ListIssues page.
     *
     * @return array The array of actions.
     */
    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Retrieve the query builder instance for the table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTableQuery(): Builder
    {
        return parent::tableQuery()
            ->where(function ($query) {
                return $query->where('owner_id', Auth::user()->id)
                    ->orWhere('responsible_id', Auth::user()->id)
                    ->orWhereHas('project', function ($query) {
                        return $query->where('owner_id', Auth::user()->id)
                            ->orWhereHas('users', function ($query) {
                                return $query->where('users.id', Auth::user()->id);
                            });
                    });
            });
    }
}
