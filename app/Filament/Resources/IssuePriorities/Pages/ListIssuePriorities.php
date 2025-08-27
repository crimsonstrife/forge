<?php

namespace App\Filament\Resources\IssuePriorities\Pages;

use App\Filament\Resources\IssuePriorities\IssuePriorityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIssuePriorities extends ListRecords
{
    protected static string $resource = IssuePriorityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
