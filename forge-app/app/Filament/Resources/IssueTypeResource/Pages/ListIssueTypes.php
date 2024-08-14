<?php

namespace App\Filament\Resources\IssueTypeResource\Pages;

use App\Filament\Resources\IssueTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListIssueTypes
 *
 * This class represents a list issue types page.
 */
class ListIssueTypes extends ListRecords
{
    protected static string $resource = IssueTypeResource::class;

    /**
     * Retrieve the header actions for the ListIssueTypes page.
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
