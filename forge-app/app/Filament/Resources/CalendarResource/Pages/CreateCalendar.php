<?php

namespace App\Filament\Resources\CalendarResource\Pages;

use App\Filament\Resources\CalendarResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateCalendar
 *
 * This class represents a create calendar page.
 */
class CreateCalendar extends CreateRecord
{
    protected static string $resource = CalendarResource::class;
}
