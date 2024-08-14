<?php

namespace App\Filament\Resources\ProjectStatusResource\Pages;

use App\Filament\Resources\ProjectStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListProjectStatuses
 *
 * This class represents a list project statuses page.
 */
class ListProjectStatuses extends ListRecords
{
    protected static string $resource = ProjectStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
