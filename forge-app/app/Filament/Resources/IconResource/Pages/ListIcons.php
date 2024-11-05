<?php

namespace App\Filament\Resources\IconResource\Pages;

use App\Filament\Resources\IconResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListIcons
 *
 * This class is responsible for listing icon records. It extends the ListRecords class and utilizes the IconResource resource.
 */
class ListIcons extends ListRecords
{
    protected static string $resource = IconResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
