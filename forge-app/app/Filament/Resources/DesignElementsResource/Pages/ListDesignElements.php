<?php

namespace App\Filament\Resources\DesignElementsResource\Pages;

use App\Filament\Resources\DesignElementsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListDesignElements
 *
 * This class represents a list design elements page.
 */
class ListDesignElements extends ListRecords
{
    protected static string $resource = DesignElementsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
