<?php

namespace App\Filament\Resources\DesignElementsResource\Pages;

use App\Filament\Resources\DesignElementsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * Class ViewDesignElement
 *
 * This class represents a page for viewing design elements.
 * It extends the ViewRecord class and is part of the DesignElementsResource.
 */
class ViewDesignElements extends ViewRecord
{
    protected static string $resource = DesignElementsResource::class;

    /**
     * Retrieves the actions available for the ViewDesignElements page.
     *
     * @return array The actions available for the ViewDesignElements page.
     */
    protected function getActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
