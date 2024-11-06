<?php

namespace App\Filament\Resources\IssueStatusResource\Pages;

use App\Filament\Resources\IssueStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListIssueStatuses
 *
 * This class extends the ListRecords class and is used to manage the listing of issue statuses
 * within the Filament resource pages of the application.
 *
 * @package App\Filament\Resources\IssueStatusResource\Pages
 */
class ListIssueStatuses extends ListRecords
{
    protected static string $resource = IssueStatusResource::class;

    /**
     * Get the header actions for the issue statuses list page.
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
