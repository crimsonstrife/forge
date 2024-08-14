<?php

namespace App\Filament\Resources\IssuePriorityResource\Pages;

use App\Filament\Resources\IssuePriorityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIssuePriorities extends ListRecords
{
    protected static string $resource = IssuePriorityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
