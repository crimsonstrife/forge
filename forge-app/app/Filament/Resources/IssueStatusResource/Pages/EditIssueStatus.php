<?php

namespace App\Filament\Resources\IssueStatusResource\Pages;

use App\Filament\Resources\IssueStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditIssueStatus
 *
 * This class extends the EditRecord class and is used to handle the editing of issue statuses
 * within the Filament resource management system.
 *
 * @package App\Filament\Resources\IssueStatusResource\Pages
 */
class EditIssueStatus extends EditRecord
{
    protected static string $resource = IssueStatusResource::class;

    /**
     * Get the header actions for the issue status edit page.
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
