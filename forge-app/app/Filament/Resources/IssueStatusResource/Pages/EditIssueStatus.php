<?php

namespace App\Filament\Resources\IssueStatusResource\Pages;

use App\Filament\Resources\IssueStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditIssueStatus
 *
 * This class represents an edit issue status page.
 */
class EditIssueStatus extends EditRecord
{
    protected static string $resource = IssueStatusResource::class;

    /**
     * Retrieves the header actions for the EditIssueStatus page.
     *
     * @return array The array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
