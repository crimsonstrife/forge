<?php

namespace App\Filament\Resources\IssueStatusResource\Pages;

use App\Filament\Resources\IssueStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListIssueStatuses
 *
 * This class represents a list issue statuses page.
 */
class ListIssueStatuses extends ListRecords
{
    protected static string $resource = IssueStatusResource::class;

    /**
     * Retrieve the header actions for the ListIssueStatuses page.
     *
     * @return array The array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
