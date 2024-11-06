<?php

namespace App\Filament\Resources\PrioritySetResource\Pages;

use App\Filament\Resources\PrioritySetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;


/**
 * Class CreatePrioritySet
 *
 * This class handles the creation of a new Priority Set record.
 * It extends the CreateRecord class provided by the Filament framework.
 *
 * @package App\Filament\Resources\PrioritySetResource\Pages
 */
class CreatePrioritySet extends CreateRecord
{
    protected static string $resource = PrioritySetResource::class;
}
