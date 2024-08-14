<?php

namespace App\Filament\Resources\IssueResource\Pages;

use App\Filament\Resources\IssueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListIssues
 *
 * This class represents a list issues page.
 */
class ListIssues extends ListRecords
{
    protected static string $resource = IssueResource::class;

    /**
     * Retrieves the header actions for the ListIssues page.
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
