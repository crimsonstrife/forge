<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * Class ViewPermission
 *
 * This class represents a view permission page for a specific record.
 */
class ViewActivity extends ViewRecord
{
    protected static string $resource = ActivityResource::class;

    protected function getActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
