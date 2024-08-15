<?php

namespace App\Filament\Resources\IssueStatusResource\Pages;

use App\Filament\Resources\IssueStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ListIssueStatuses
 *
 * This class represents a list issue statuses page.
 */
class ListIssueStatuses extends ListRecords
{
    protected static string $resource = IssueStatusResource::class;

    /**
     * Retrieve the actions for the ListIssueStatuses page.
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
     * Retrieve the table query for the ListIssueStatuses page.
     *
     * @return Builder The table query.
     */
    protected function getTableQuery(): Builder
    {
        return parent::tableQuery()
            ->whereNull('project_id');
    }
}
