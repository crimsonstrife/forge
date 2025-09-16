<?php

namespace App\Filament\Resources\IssueStatuses\Pages;

use App\Filament\Resources\IssueStatuses\IssueStatusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIssueStatuses extends ListRecords
{
    protected static string $resource = IssueStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
