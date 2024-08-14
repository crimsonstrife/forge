<?php

namespace App\Filament\Resources\ProjectTypeResource\Pages;

use App\Filament\Resources\ProjectTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditProjectType
 *
 * This class represents an edit project type page.
 */
class EditProjectType extends EditRecord
{
    protected static string $resource = ProjectTypeResource::class;

    /**
     * Retrieves the header actions for the EditProjectType page.
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
