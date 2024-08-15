<?php

namespace App\Filament\Resources\IssueStatusResource\Pages;

use App\Filament\Resources\IssueStatusResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * ViewIssueStatus class.
 *
 * This class represents a page for viewing an issue status record.
 * It extends the ViewRecord class.
 *
 * @package Filament\Resources\IssueStatusResource\Pages
 */
class ViewIssueStatus extends ViewRecord
{
    protected static string $resource = IssueStatusResource::class;

    /**
     * Retrieves the available actions for the ViewIssueStatus page.
     *
     * @return array The array of available actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
