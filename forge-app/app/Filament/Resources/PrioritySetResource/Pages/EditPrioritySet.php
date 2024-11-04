<?php

namespace App\Filament\Resources\PrioritySetResource\Pages;

use App\Filament\Resources\PrioritySetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrioritySet extends EditRecord
{
    protected static string $resource = PrioritySetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
