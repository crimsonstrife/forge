<?php

namespace App\Filament\Resources\IconResource\Pages;

use App\Filament\Resources\IconResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;


/**
 * Class ListIcons
 *
 * This class extends the ListRecords class and is used to manage the listing of icons
 * in the Filament resource.
 *
 * @package App\Filament\Resources\IconResource\Pages
 */
class ListIcons extends ListRecords
{
    protected static string $resource = IconResource::class;

    /**
     * Get the header actions for the list icons page.
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
