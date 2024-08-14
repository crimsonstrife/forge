<?php

namespace App\Filament\Resources\TimesheetResource\Pages;

use App\Filament\Resources\TimesheetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateTimesheet
 *
 * This class represents a create timesheet page.
 */
class CreateTimesheet extends CreateRecord
{
    protected static string $resource = TimesheetResource::class;
}
