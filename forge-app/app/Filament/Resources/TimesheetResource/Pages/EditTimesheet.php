<?php

namespace App\Filament\Resources\TimesheetResource\Pages;

use App\Filament\Resources\TimesheetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditTimesheet
 *
 * This class represents an edit timesheet page.
 */
class EditTimesheet extends EditRecord
{
    protected static string $resource = TimesheetResource::class;

    /**
     * Retrieve the header actions for the EditTimesheet page.
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
