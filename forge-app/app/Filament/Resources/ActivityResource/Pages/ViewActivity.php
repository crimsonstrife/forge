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

    /**
     * Retrieve the actions available for the activity resource page.
     *
     * @return array The actions available for the activity resource page.
     */
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
