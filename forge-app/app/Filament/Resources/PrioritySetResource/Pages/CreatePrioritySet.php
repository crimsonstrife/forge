<?php

namespace App\Filament\Resources\PrioritySetResource\Pages;

use App\Filament\Resources\PrioritySetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreatePrioritySet
 *
 * This class extends the CreateRecord class specifically for creating PrioritySet records.
 * It specifies the resource type as PrioritySetResource.
 */
class CreatePrioritySet extends CreateRecord
{
    protected static string $resource = PrioritySetResource::class;
}
