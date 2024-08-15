<?php

namespace App\Filament\Resources\IssueTypeResource\Pages;

use App\Filament\Resources\IssueTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * This class is responsible for viewing issue types.
 */
class ViewIssueType extends ViewRecord
{
    protected static string $resource = IssueTypeResource::class;

    /**
     * Retrieves an array of actions available.
     *
     * @return array An array of action instances.
     */
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
