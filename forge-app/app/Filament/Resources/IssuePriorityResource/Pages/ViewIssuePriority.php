<?php

namespace App\Filament\Resources\IssuePriorityResource\Pages;

use App\Filament\Resources\IssuePriorityResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * ViewIssuePriority class.
 *
 * This class represents a page for viewing an issue priority record.
 * It extends the ViewRecord class.
 *
 * @package Filament\Resources\IssuePriorityResource\Pages
 */
class ViewIssuePriority extends ViewRecord
{
    protected static string $resource = IssuePriorityResource::class;

    /**
     * Retrieve the available actions for the ViewIssuePriority page.
     *
     * @return array The array of available actions.
     */
    protected function getActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
