<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditProject
 *
 * This class represents an edit project page.
 */
class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    /**
     * Retrieve the header actions for the EditProject page.
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
