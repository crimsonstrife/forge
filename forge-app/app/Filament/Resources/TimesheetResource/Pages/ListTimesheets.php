<?php

namespace App\Filament\Resources\TimesheetResource\Pages;

use App\Filament\Resources\TimesheetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListTimesheets
 *
 * This class represents a list timesheets page.
 */
class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    /**
     * Returns an array of header actions for the ListTimesheets page.
     *
     * @return array The array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
