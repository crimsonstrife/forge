<?php

namespace App\Filament\Resources\PrioritySetResource\Pages;

use App\Filament\Resources\PrioritySetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * A class that extends ListRecords specifically for managing priority sets.
 */
class ListPrioritySets extends ListRecords
{
    protected static string $resource = PrioritySetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
