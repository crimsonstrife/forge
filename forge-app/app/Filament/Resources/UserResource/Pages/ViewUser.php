<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * Class ViewUser
 *
 * This class represents a view user page for a specific record.
 */
class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Retrieves the actions available for the user resource page.
     *
     * @return array The array of actions available for the user resource page.
     */
    protected function getActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
