<?php

namespace App\Filament\Resources\IssueResource\Pages;

use App\Filament\Resources\IssueResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditIssue
 *
 * This class represents an edit issue page.
 */
class EditIssue extends EditRecord
{
    protected static string $resource = IssueResource::class;

    /**
     * Retrieves the header actions for the EditIssue page.
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
