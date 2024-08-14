<?php

namespace App\Filament\Resources\DesignElementsResource\Pages;

use App\Filament\Resources\DesignElementsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditDesignElements
 *
 * This class represents an edit design elements page.
 */
class EditDesignElements extends EditRecord
{
    protected static string $resource = DesignElementsResource::class;

    /**
     * Retrieves the header actions for the EditDesignElements page.
     *
     * @return array The array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
