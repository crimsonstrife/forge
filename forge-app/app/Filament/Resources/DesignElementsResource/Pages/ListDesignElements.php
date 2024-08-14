<?php

namespace App\Filament\Resources\DesignElementsResource\Pages;

use App\Filament\Resources\DesignElementsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDesignElements extends ListRecords
{
    protected static string $resource = DesignElementsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
