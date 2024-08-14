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

    /**
     * Retrieves the header actions for the ListDesignElements page.
     *
     * @return array The array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
