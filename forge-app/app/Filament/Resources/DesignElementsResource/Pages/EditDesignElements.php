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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
