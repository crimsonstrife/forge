<?php

namespace App\Filament\Resources\ProjectStatusResource\Pages;

use App\Filament\Resources\ProjectStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditProjectStatus
 *
 * This class represents an edit project status page.
 */
class EditProjectStatus extends EditRecord
{
    protected static string $resource = ProjectStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
